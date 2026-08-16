<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /** group => [key => [label, type, hint]] */
    public static function schema(): array
    {
        return [
            'Business' => [
                'site_name' => ['label' => 'Business name', 'type' => 'text'],
                'tagline' => ['label' => 'Tagline', 'type' => 'text'],
                'logo' => ['label' => 'Logo', 'type' => 'image', 'hint' => 'Transparent PNG or SVG. Falls back to the business name if empty.'],
                'founded_year' => ['label' => 'Operating since', 'type' => 'text'],
            ],
            'Contact' => [
                'phone' => ['label' => 'Phone number', 'type' => 'text', 'hint' => 'Shown as a tap-to-call link.'],
                'phone_alt' => ['label' => 'Other phone numbers', 'type' => 'textarea', 'hint' => 'One per line. Shown alongside the main number in the footer and on the contact page.'],
                'whatsapp_number' => ['label' => 'WhatsApp number', 'type' => 'text', 'hint' => 'International format with no leading zero, e.g. 2347033758671. Every Book Now button uses this.'],
                'email' => ['label' => 'Email address', 'type' => 'text'],
                'address_primary' => ['label' => 'Address line 1', 'type' => 'text'],
                'address_secondary' => ['label' => 'Address line 2', 'type' => 'text'],
                'business_hours' => ['label' => 'Business hours', 'type' => 'text'],
            ],
            'Homepage' => [
                'hero_eyebrow' => ['label' => 'Hero eyebrow', 'type' => 'text'],
                'hero_heading' => ['label' => 'Hero heading', 'type' => 'text'],
                'hero_subheading' => ['label' => 'Hero subheading', 'type' => 'textarea'],
                'hero_video' => ['label' => 'Hero background video URL', 'type' => 'text', 'hint' => 'Leave empty to use the image instead.'],
                'hero_image' => ['label' => 'Hero background image', 'type' => 'image'],
                'fleet_note' => ['label' => 'Note above the fleet grid', 'type' => 'textarea'],
                'about_heading' => ['label' => 'About heading', 'type' => 'text'],
                'about_body' => ['label' => 'About paragraph', 'type' => 'textarea'],
                'trust_points' => ['label' => 'Trust checklist', 'type' => 'textarea', 'hint' => 'One per line.'],
            ],
            'Numbers' => [
                'stat_years' => ['label' => 'Years of service', 'type' => 'text'],
                'stat_clients' => ['label' => 'Clients served', 'type' => 'text'],
                'stat_vehicles' => ['label' => 'Vehicles in fleet', 'type' => 'text'],
                'stat_rating' => ['label' => 'Average rating', 'type' => 'text'],
            ],
            'Social' => [
                'facebook_url' => ['label' => 'Facebook', 'type' => 'text'],
                'instagram_url' => ['label' => 'Instagram', 'type' => 'text'],
                'twitter_url' => ['label' => 'X / Twitter', 'type' => 'text'],
                'tiktok_url' => ['label' => 'TikTok', 'type' => 'text'],
            ],
            'Search engines' => [
                'meta_title' => ['label' => 'Default page title', 'type' => 'text'],
                'meta_description' => ['label' => 'Default description', 'type' => 'textarea'],
                'meta_keywords' => ['label' => 'Keywords', 'type' => 'textarea'],
                'og_image' => ['label' => 'Social share image', 'type' => 'image'],
                'analytics_head' => ['label' => 'Analytics / verification code', 'type' => 'textarea', 'hint' => 'Pasted into the <head> of every page. Google Analytics, Search Console, Meta Pixel.'],
            ],
        ];
    }

    public function edit()
    {
        return view('admin.settings', [
            'schema' => static::schema(),
            'values' => Setting::allValues(),
        ]);
    }

    public function update(Request $request)
    {
        foreach (static::schema() as $group => $fields) {
            foreach ($fields as $key => $field) {
                if ($field['type'] === 'image') {
                    if ($request->hasFile($key)) {
                        $existing = Setting::get($key);
                        if ($existing) {
                            Storage::disk('public')->delete($existing);
                        }
                        Setting::put($key, $request->file($key)->store('uploads', 'public'), $group);
                    } elseif ($request->boolean('remove_' . $key)) {
                        $existing = Setting::get($key);
                        if ($existing) {
                            Storage::disk('public')->delete($existing);
                        }
                        Setting::put($key, null, $group);
                    }

                    continue;
                }

                Setting::put($key, $request->input($key), $group);
            }
        }

        return back()->with('status', 'Settings saved.');
    }
}
