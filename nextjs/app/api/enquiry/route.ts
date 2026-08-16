import { NextResponse } from 'next/server';
import nodemailer from 'nodemailer';
import { composeMessage, type Enquiry } from '@/lib/enquiry';

/* --------------------------------------------------------------------------
   Enquiry endpoint
   --------------------------------------------------------------------------
   Receives a booking enquiry and emails it to the business inbox over the
   Hostinger mailbox's own SMTP. Runs on Vercel's Node runtime — nodemailer
   opens a TCP connection, which the Edge runtime cannot do.
   -------------------------------------------------------------------------- */

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

const FIELD_LIMITS: Record<string, number> = {
  name: 120, phone: 40, email: 160, vehicle: 120, service: 120,
  pickupDate: 40, pickupLocation: 160, destination: 160, days: 10, notes: 2000,
  source: 200,
};

/** Header injection guard: CR/LF in a header value can forge extra headers. */
const oneLine = (v: string) => v.replace(/[\r\n]+/g, ' ').trim();

function clean(body: Record<string, unknown>): Enquiry {
  const out: Record<string, string> = {};

  for (const [key, max] of Object.entries(FIELD_LIMITS)) {
    const raw = body[key];
    if (typeof raw !== 'string') continue;
    const value = raw.trim().slice(0, max);
    if (value) out[key] = value;
  }

  return out as Enquiry;
}

export async function POST(request: Request) {
  let body: Record<string, unknown>;

  try {
    body = await request.json();
  } catch {
    return NextResponse.json({ error: 'Malformed request.' }, { status: 400 });
  }

  // Honeypot. Real people never fill a hidden field; bots fill everything.
  if (typeof body.website === 'string' && body.website.trim() !== '') {
    return NextResponse.json({ ok: true });
  }

  const enquiry = clean(body);

  if (!enquiry.name || !enquiry.phone) {
    return NextResponse.json(
      { error: 'Please include your name and phone number.' },
      { status: 422 }
    );
  }

  const { EMAIL_USER, EMAIL_PASSWORD, EMAIL_TO, SMTP_HOST, SMTP_PORT } = process.env;

  if (!EMAIL_USER || !EMAIL_PASSWORD) {
    // Configuration fault, not the visitor's. Say so plainly and log it, rather
    // than reporting a success the business will never see.
    console.error('[enquiry] EMAIL_USER / EMAIL_PASSWORD are not set');
    return NextResponse.json(
      { error: 'Email is not configured on the server. Please use WhatsApp.' },
      { status: 503 }
    );
  }

  const port = Number(SMTP_PORT ?? 465);

  const transport = nodemailer.createTransport({
    host: SMTP_HOST ?? 'smtp.hostinger.com',
    port,
    secure: port === 465,
    auth: { user: EMAIL_USER, pass: EMAIL_PASSWORD },
  });

  const subject = enquiry.vehicle
    ? `Booking enquiry — ${enquiry.vehicle}`
    : enquiry.service
      ? `Booking enquiry — ${enquiry.service}`
      : 'Booking enquiry';

  const text = composeMessage(enquiry);

  try {
    await transport.sendMail({
      // From must be the authenticated mailbox or Hostinger rejects the message.
      from: `"Website enquiry" <${EMAIL_USER}>`,
      to: EMAIL_TO || EMAIL_USER,
      // Replying in the inbox goes to the customer, not back to ourselves.
      replyTo: enquiry.email ? oneLine(enquiry.email) : undefined,
      subject: oneLine(subject),
      text,
      html: `<pre style="font:14px/1.6 ui-monospace,monospace;white-space:pre-wrap">${
        text.replace(/[<>&]/g, (c) => ({ '<': '&lt;', '>': '&gt;', '&': '&amp;' }[c] as string))
      }</pre>`,
    });
  } catch (error) {
    console.error('[enquiry] send failed:', error);
    return NextResponse.json(
      { error: 'We could not send that. Please try WhatsApp instead.' },
      { status: 502 }
    );
  }

  return NextResponse.json({ ok: true });
}
