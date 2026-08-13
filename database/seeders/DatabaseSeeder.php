<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Faq;
use App\Models\Location;
use App\Models\Page;
use App\Models\Post;
use App\Models\ServiceType;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->admin();
        $this->settings();
        $this->fleet();
        $this->places();
        $this->services();
        $this->words();
    }

    /**
     * Copy a bundled seed photo into the public uploads disk and return the
     * stored path, matching what the dashboard's image uploader produces.
     */
    protected function media(string $file): ?string
    {
        $source = database_path('seeders/media/' . $file);

        if (! is_file($source)) {
            return null;
        }

        $target = 'uploads/' . $file;

        if (! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, file_get_contents($source));
        }

        return $target;
    }

    protected function admin(): void
    {
        // Assigned directly rather than through updateOrCreate(): `is_admin` is not in
        // the stock Laravel User model's $fillable, so mass assignment would silently
        // drop it and lock the new account out of the dashboard.
        $user = User::firstOrNew(['email' => env('ADMIN_EMAIL', 'admin@example.com')]);
        $user->name = env('ADMIN_NAME', 'Site Admin');
        $user->password = Hash::make(env('ADMIN_PASSWORD', 'change-this-password'));
        $user->is_admin = true;
        $user->save();
    }

    protected function settings(): void
    {
        $values = [
            'Business' => [
                'site_name' => env('APP_NAME', 'Folafemz Global NIG LTD'),
                'tagline' => 'Chauffeur-driven cars, buses and logistics across Nigeria',
                'founded_year' => '2020',
            ],
            'Contact' => [
                'phone' => '+234 814 991 6721',
                'whatsapp_number' => '2348149916721',
                'email' => 'riotech2222@gmail.com',
                'address_primary' => 'Lagos — Victoria Island',
                'address_secondary' => 'Abuja — Wuse II',
                'business_hours' => 'Dispatch desk open 24 hours, every day',
            ],
            'Homepage' => [
                'hero_eyebrow' => 'Lagos · Abuja · Port Harcourt · Ibadan',
                'hero_heading' => 'Vehicles and drivers you can build a schedule around',
                'hero_subheading' => 'Chauffeur-driven cars, self-drive hire, airport pickups, interstate travel, moving trucks and security escorts. Quoted in minutes on WhatsApp, delivered to your door.',
                'fleet_note' => 'Rates shown are per day within Lagos and Abuja, and include the driver, fuel inside the city and insurance. Other cities, interstate routes, self-drive and moving trucks are quoted on request.',
                'about_heading' => 'Built for people who cannot afford to be late',
                'about_body' => 'Most hire problems are not car problems. They are dispatch problems: nobody answers, the driver leaves late, the vehicle turns up dirty. We run a maintained fleet, vetted drivers who know their routes, and a desk that picks up. Corporate accounts, weddings, airport runs and long-term leases all run to the same standard.',
                'trust_points' => "Quoted on WhatsApp in minutes\nDelivered anywhere in the cities we cover\nInsurance on every vehicle\nBackup vehicle if yours develops a fault\nDaily, weekly and monthly terms\nOne agreed price, no surprises on the day",
            ],
            'Numbers' => [
                // CHANGE ME: these are placeholders. Publish your real figures —
                // invented statistics are a liability, not marketing.
                'stat_years' => '6',
                'stat_clients' => '1,500',
                'stat_vehicles' => '40',
                'stat_rating' => '4.8',
            ],
            'Social' => [
                'facebook_url' => '',
                'instagram_url' => '',
                'twitter_url' => '',
                'tiktok_url' => '',
            ],
            'Search engines' => [
                'meta_title' => 'Car Hire Nigeria | Chauffeur, Self Drive, Buses & Moving Trucks',
                'meta_description' => 'Daily, weekly and monthly car hire in Lagos, Abuja, Port Harcourt and Ibadan. SUVs, buses, luxury cars, moving trucks and security escorts, with driver and insurance included.',
                'meta_keywords' => 'car hire nigeria, car rental lagos, car rental abuja, chauffeur service lagos, moving truck hire, self drive car rental nigeria, airport pickup lagos',
            ],
        ];

        foreach ($values as $group => $pairs) {
            foreach ($pairs as $key => $value) {
                Setting::put($key, $value, $group);
            }
        }

        if ($hero = $this->media('hero-night-highway.jpg')) {
            Setting::put('hero_image', $hero, 'Homepage');
        }
    }

    protected function fleet(): void
    {
        $categories = ['SUVs', 'Sedans', 'Buses & Minivans', 'Premium & Luxury', 'Logistics & Other'];
        foreach ($categories as $i => $name) {
            VehicleCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i]
            );
        }

        $lookup = VehicleCategory::pluck('id', 'slug');

        // [name, category, daily, airport, seats, image, description]
        $vehicles = [
            ['Toyota Prado 2022', 'suvs', 170000, 100000, '7 seats', 'suv-black.jpg',
                'The default choice for executive movement in Lagos and Abuja. High clearance for bad roads, quiet on the highway, and comfortable enough for a full day of meetings.'],
            ['Lexus GX 460', 'suvs', 200000, 120000, '7 seats', 'suv-dark-estate.jpg',
                'A step up from the Prado in cabin finish and ride quality. Popular for hosting visiting directors and for wedding convoys where the car is part of the picture.'],
            ['Toyota Land Cruiser', 'suvs', 400000, 200000, '7 seats', 'suv-white-luxury.jpg',
                'Full-size, heavy and unbothered by long distances or rough surfaces. The vehicle we send for interstate convoys and site visits far outside the city.'],
            ['Toyota Hilux', 'suvs', 150000, 90000, '5 seats', 'suv-black.jpg',
                'Double-cab pickup for field teams. Load bed for equipment, and the clearance to reach sites that a saloon cannot.'],
            ['Toyota Camry 2014', 'sedans', 130000, 70000, '4 seats', 'sedan-blue.jpg',
                'Clean, quiet and economical. The sensible pick for airport runs, city meetings and daily staff transport where an SUV is more car than you need.'],
            ['Toyota Camry 2010', 'sedans', 100000, 60000, '4 seats', 'sedan-silver.jpg',
                'Our entry-level saloon. Same driver standard and same insurance as everything else in the fleet, at the lowest daily rate we offer.'],
            ['Toyota Sienna 2014', 'buses-minivans', 160000, 100000, '7 seats', 'bus-hiace.jpg',
                'Family and small-group transport with proper luggage space behind the third row. Sliding doors make it easy in tight compounds and car parks.'],
            ['Toyota Sienna 2006', 'buses-minivans', 140000, 70000, '7 seats', 'bus-hiace.jpg',
                'The budget seven-seater. Well maintained, air conditioning throughout, and the cheapest way to move a family with luggage.'],
            ['Toyota Hiace Bus', 'buses-minivans', 200000, 100000, '14 seats', 'bus-hiace.jpg',
                'The workhorse for staff runs, church trips and airport groups. Fourteen seats, air conditioned, with room for bags on a longer journey.'],
            ['Toyota Coaster Bus', 'buses-minivans', 300000, 200000, '30 seats', 'interstate-minibus.jpg',
                'Thirty seats for conferences, corporate away days and large church or school movements. Comes with a driver used to managing a full bus.'],
            ['Mercedes Sprinter', 'buses-minivans', 700000, null, '18 seats', 'interstate-minibus.jpg',
                'High-roof executive shuttle. Stand-up cabin, individual seats and a far quieter ride than a standard bus — used for VIP delegations and hotel transfers.'],
            ['Lexus LX 570', 'premium-luxury', 450000, 200000, '7 seats', 'suv-white-luxury.jpg',
                'Top of the standard fleet. Full-size luxury SUV for principals, visiting executives and weddings where the lead car matters.'],
            ['Mercedes G-Class', 'premium-luxury', null, null, '5 seats', 'suv-g-class.jpg',
                'Statement vehicle for weddings, photo shoots and events. Limited availability, so book early. Priced per engagement rather than per day.'],
            ['Armoured B6 SUV', 'premium-luxury', null, null, '5 seats', 'suv-g-class.jpg',
                'B6-rated armoured vehicle with a security-trained driver. Supplied with documentation and, where required, an escort team. Quoted per assignment after a short brief.'],
            ['Moving Truck', 'logistics-other', null, null, null, 'moving-van.jpg',
                'Home and office relocations with a loading crew, wrapping materials and a driver who knows how to get a truck into a Lagos estate.'],
            ['Interstate Travel', 'logistics-other', null, null, null, 'interstate-minibus.jpg',
                'Point-to-point road travel between states with a rested, route-familiar driver. Quoted by route, not by day.'],
            ['Security Escort', 'logistics-other', null, null, null, 'night-drive.jpg',
                'Trained escort teams and lead vehicles for sensitive movements, cash-in-transit support and VIP protection.'],
            ['Self Drive Hire', 'logistics-other', null, null, null, 'interior-console.jpg',
                'Drive it yourself. Valid licence, a verification check and a refundable deposit; insurance and papers are handled by us.'],
        ];

        foreach ($vehicles as $i => [$name, $category, $daily, $airport, $seats, $image, $description]) {
            Vehicle::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'vehicle_category_id' => $lookup[$category] ?? null,
                    'daily_rate' => $daily,
                    'secondary_rate' => $airport,
                    'rate_note' => $daily ? null : 'Request a quote',
                    'seats' => $seats,
                    'description' => $description,
                    'image' => $this->media($image),
                    'sort_order' => $i,
                    'is_featured' => $i < 3,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function places(): void
    {
        $cities = [
            [
                'name' => 'Lagos', 'state' => 'Lagos State', 'rating' => '4.9/5',
                'tagline' => 'Island and mainland coverage, airport runs around the clock',
                'areas_summary' => 'Victoria Island, Lekki, Ikeja, Ikoyi',
                'office_address' => 'Victoria Island, Lagos',
                'airport_branch' => 'Murtala Muhammed International Airport',
                'hero_image' => 'chauffeur-service.jpg',
                'intro' => 'Lagos punishes bad planning. A meeting on the Island and another on the mainland can be forty minutes apart or three hours apart depending on when you leave, and no amount of horsepower fixes that. What fixes it is a driver who knows which bridge to take at four in the afternoon. Our Lagos fleet runs from Victoria Island with a standing presence at both terminals of Murtala Muhammed, so airport pickups do not depend on someone setting off from across the city when your flight lands.',
                'highlights' => "Corporate fleet management and monthly contracts\nIsland to mainland commuting with fixed pricing\nWedding cars and decorated convoys\nAirport transfers with flight tracking\nMoving trucks with a loading crew",
                'locations' => ['Lekki Phase 1', 'Victoria Island', 'Ikeja', 'Ikoyi', 'Ajah', 'Yaba', 'Surulere', 'Gbagada', 'Maryland', 'Festac'],
            ],
            [
                'name' => 'Abuja', 'state' => 'Federal Capital Territory', 'rating' => '4.8/5',
                'tagline' => 'Government, diplomatic and conference transport',
                'areas_summary' => 'Maitama, Wuse, Garki, Asokoro',
                'office_address' => 'Wuse II, Abuja',
                'airport_branch' => 'Nnamdi Azikiwe International Airport',
                'hero_image' => 'suv-white-luxury.jpg',
                'intro' => 'Abuja work is protocol work. Delegations arrive on a fixed schedule, sit in sessions that overrun, and need vehicles waiting rather than summoned. Our Abuja desk runs from Wuse II and handles the things that go wrong around conferences: a session that ends two hours late, a principal who needs to leave early, a delegation that grew by four people overnight. Vehicles are presented clean, drivers are briefed on the itinerary, and the airport road is covered at any hour.',
                'highlights' => "Diplomatic and protocol vehicles\nConference and event fleets\nExecutive transport with briefed drivers\nAirport transfers at any hour\nArmoured vehicles on request",
                'locations' => ['Wuse 2', 'Maitama', 'Asokoro', 'Garki', 'Gwarinpa', 'Central Business District', 'Utako', 'Jabi', 'Kubwa', 'Wuse Zone 4'],
            ],
            [
                'name' => 'Port Harcourt', 'state' => 'Rivers State', 'rating' => '4.8/5',
                'tagline' => 'Oil and gas site visits, corporate transport',
                'areas_summary' => 'GRA, Trans Amadi, Eliozu',
                'office_address' => 'GRA Phase 2, Port Harcourt',
                'airport_branch' => 'Port Harcourt International Airport',
                'hero_image' => 'night-drive.jpg',
                'intro' => 'Port Harcourt hire is mostly industrial. Crews need to reach sites down roads that chew up low vehicles, on schedules set by shift patterns rather than office hours. We keep high-clearance vehicles and drivers who have done the Trans Amadi and Eliozu runs often enough to route around the flooding. Escort arrangements are available where a client requires them, arranged in advance rather than improvised.',
                'highlights' => "High-clearance vehicles for site visits\nShift-pattern scheduling for crews\nAirport transfers\nEvent and conference transport\nSecurity escorts on request",
                'locations' => ['GRA Port Harcourt', 'Trans Amadi', 'Eliozu', 'Rumuokoro', 'Woji'],
            ],
            [
                'name' => 'Ibadan', 'state' => 'Oyo State', 'rating' => '4.8/5',
                'tagline' => 'Executive travel and long-term corporate hire',
                'areas_summary' => 'Bodija, Ring Road, Jericho',
                'office_address' => 'Bodija, Ibadan',
                'airport_branch' => 'Ibadan Airport',
                'hero_image' => 'sedan-blue.jpg',
                'intro' => 'Ibadan sits close enough to Lagos that a lot of our work here is the Lagos–Ibadan run itself: staff moving between offices, families travelling for ceremonies, and companies that would rather not put an employee on the expressway in their own car. We price that route point to point. Inside the city we handle the usual mix of executive hire, weddings and monthly contracts.',
                'highlights' => "Lagos–Ibadan expressway runs priced by route\nCorporate and executive transport\nWedding hire\nMonthly and quarterly lease terms",
                'locations' => ['Bodija', 'Jericho', 'Ring Road', 'Akobo'],
            ],
        ];

        foreach ($cities as $i => $data) {
            $locations = $data['locations'];
            $image = $data['hero_image'] ?? null;
            unset($data['locations'], $data['hero_image']);

            $data['meta_title'] = 'Car Hire in ' . $data['name'] . ' | Chauffeur, Self Drive & Bus Rental';
            $data['meta_description'] = 'Rent a car with a driver in ' . $data['name'] . '. SUVs, saloons, buses and moving trucks, with insurance and fuel inside the city included. Quoted on WhatsApp in minutes.';

            City::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + [
                    'hero_image' => $image ? $this->media($image) : null,
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );

            $city = City::where('slug', Str::slug($data['name']))->first();

            foreach ($locations as $j => $area) {
                Location::updateOrCreate(
                    ['slug' => Str::slug($area)],
                    [
                        'city_id' => $city->id,
                        'name' => $area,
                        'blurb' => 'We deliver to ' . $area . ' and pick up from it daily. Give us the address and a time and the vehicle will be outside, not "on the way".',
                        'sort_order' => $j,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    protected function services(): void
    {
        $services = [
            [
                'Moving Truck', 'Hire Moving Truck', 'moving-van.jpg',
                'Home moves, office relocations and furniture transport with a loading crew and wrapping materials included.',
                '<p>A move goes wrong in the gaps: the truck arrives without wrapping, nobody thought about the estate gate closing at 8pm, the lift is booked for the wrong day. We quote the whole job rather than the vehicle.</p>
                 <h3>What comes with the truck</h3>
                 <ul><li>A loading crew sized to the job, not one driver and an apprentice</li><li>Wrapping material, blankets and straps for furniture and appliances</li><li>A driver used to getting a truck through estate gates and narrow compounds</li><li>Loading, transport, offloading and rough placement in the new space</li></ul>
                 <h3>Before you book</h3>
                 <p>Tell us the number of rooms, the floor you are on at both ends, and whether either building has a working lift. Those three facts move a quote more than distance does. For office moves, tell us whether the IT equipment is being handled by your own team.</p>
                 <h3>Timing</h3>
                 <p>Weekend moves book out first, particularly at month end. A weekday move is usually cheaper and always faster, because you are not competing for the lift or the gate.</p>',
            ],
            [
                'Airport Transfer', 'Book Airport Transfer', 'chauffeur-service.jpg',
                'Flight-tracked pickups and drop-offs with waiting time included, at both Lagos and Abuja terminals.',
                '<p>We track the flight, not the booking time. If you land ninety minutes late the driver is still there, and you are not charged for the delay.</p>
                 <h3>How the pickup works</h3>
                 <ul><li>Send the flight number when you book — that is what we monitor</li><li>The driver waits inside arrivals with a name board</li><li>Sixty minutes of waiting time is included after landing on domestic flights, ninety on international</li><li>One agreed price, including parking and tolls</li></ul>
                 <h3>Coverage</h3>
                 <p>We hold vehicles at Murtala Muhammed in Lagos and Nnamdi Azikiwe in Abuja, both terminals, at any hour. Port Harcourt and Ibadan airports are covered on advance booking.</p>
                 <h3>Departures</h3>
                 <p>For outbound flights we work backwards from your check-in time and the traffic on the day, and we will tell you honestly if the pickup time you have asked for is too late.</p>',
            ],
            [
                'Wedding Car', 'Hire Wedding Car', 'suv-g-class.jpg',
                'Decorated vehicles, uniformed chauffeurs and convoy arrangements built around the day\'s timetable.',
                '<p>Wedding transport is a timetable problem wearing a nice suit. The cars matter, but what actually saves the day is a driver who knows he is collecting the couple from the church at 2pm and where he is parking until then.</p>
                 <h3>What we arrange</h3>
                 <ul><li>A lead car for the couple, decorated to your colours</li><li>Convoy vehicles for family and the bridal party</li><li>Uniformed chauffeurs briefed on the full run of the day</li><li>Buses for guest movement between the ceremony and reception</li></ul>
                 <h3>Booking window</h3>
                 <p>Book six to eight weeks out for a Saturday in December or January — those dates go first and they go early. For the statement vehicles, longer.</p>
                 <h3>On the day</h3>
                 <p>We ask for one contact who is not the couple. Somebody has to be reachable at 9am, and it should not be the bride.</p>',
            ],
            [
                'Corporate Car', 'Hire Corporate Car', 'sedan-blue.jpg',
                'Monthly and quarterly contracts with a dedicated driver, one invoice and a named account manager.',
                '<p>Running your own pool cars means running a small fleet business on the side: maintenance, licensing, driver cover when someone is sick. A monthly contract puts that on us.</p>
                 <h3>How contracts work</h3>
                 <ul><li>A dedicated vehicle and a dedicated driver, not whoever is free</li><li>Maintenance, servicing, insurance and papers handled by us</li><li>A replacement vehicle if yours goes in for work</li><li>One invoice a month and a named person to call</li></ul>
                 <h3>Terms</h3>
                 <p>Monthly is the shortest contract we do. Quarterly and annual terms are meaningfully cheaper per day, and for annual we will hold a specific vehicle for your account.</p>
                 <h3>Drivers</h3>
                 <p>Contract drivers are vetted, and you meet the driver before the contract starts. If the fit is wrong in the first fortnight we change them without argument.</p>',
            ],
            [
                'Luxury Car', 'Rent Luxury Car', 'premium-front.jpg',
                'Premium sedans and SUVs for visiting executives, photo shoots and events where the car is part of the impression.',
                '<p>Sometimes the vehicle is doing a job beyond transport — collecting an investor, carrying a principal into a venue, sitting in a shot. Those bookings are priced per engagement rather than per day.</p>
                 <h3>Availability</h3>
                 <p>The premium fleet is small and moves quickly. Tell us the date, the duration and roughly what the vehicle is for, and we will tell you honestly what is free.</p>',
            ],
            [
                'Security Escort', 'Hire Security Escort', 'night-drive.jpg',
                'Trained escort teams and lead vehicles for sensitive movements, arranged in advance after a short brief.',
                '<p>Escort work is arranged, never improvised. We take a brief on the route, the timing and the risk profile, then propose a team and a vehicle configuration to match.</p>
                 <h3>What a brief covers</h3>
                 <ul><li>Route, timing and any fixed appointments along the way</li><li>Number of principals and vehicles in the movement</li><li>Whether the client requires armed or unarmed personnel</li><li>Coordination with any existing security detail</li></ul>
                 <p>All escort personnel work within Nigerian law and with the appropriate authorisations. We will decline work we cannot staff properly.</p>',
            ],
            [
                'Armoured SUV', 'Hire Armoured SUV', 'suv-g-class.jpg',
                'B6 and B7 rated vehicles with security-trained drivers, supplied with documentation.',
                '<p>Armoured vehicles are quoted per assignment. Availability is limited and paperwork takes time, so bring us the requirement early rather than the day before.</p>
                 <h3>What we need to quote</h3>
                 <ul><li>Protection level required (B6 is standard; B7 on request)</li><li>Duration and route</li><li>Whether an escort team is needed alongside the vehicle</li></ul>
                 <p>Every armoured vehicle is supplied with its documentation and a driver trained for the vehicle\'s weight and handling, which is considerably different from a standard SUV.</p>',
            ],
            [
                'Self Drive', 'Rent Self Drive Car', 'interior-console.jpg',
                'Drive it yourself, with insurance, documentation and roadside cover handled by us.',
                '<p>Self drive is the cheapest way to hire, because you are not paying for a driver\'s day. It comes with more paperwork, for obvious reasons.</p>
                 <h3>What you need</h3>
                 <ul><li>A valid Nigerian driver\'s licence held for at least two years</li><li>Government-issued ID and proof of address</li><li>A refundable deposit, returned after the vehicle is checked back in</li><li>A short verification call — usually same day</li></ul>
                 <h3>What is included</h3>
                 <p>Comprehensive insurance, all vehicle papers, and roadside assistance for the duration of the hire. Fuel is on you. The vehicle goes out full and should come back full.</p>',
            ],
            [
                'Interstate Travel', 'Book Interstate Travel', 'interstate-minibus.jpg',
                'Long-distance road travel with rested, route-familiar drivers, priced by route rather than by day.',
                '<p>Interstate is priced by route, because a Lagos–Benin run and a Lagos–Kano run are not the same job even if both take a day on paper.</p>
                 <h3>How we run long distance</h3>
                 <ul><li>Drivers who have run the specific route before</li><li>Departure timed so the difficult stretches are covered in daylight</li><li>A second driver on routes beyond a single safe driving day</li><li>Fuel and tolls built into the quoted price</li></ul>
                 <h3>Common routes</h3>
                 <p>Lagos–Abuja, Lagos–Ibadan, Lagos–Benin, Abuja–Kaduna and Abuja–Jos are routine for us. Anywhere else, give us the destination and we will price it.</p>',
            ],
            [
                'Coaster Bus', 'Hire Coaster Bus', 'interstate-minibus.jpg',
                'Group movement for staff runs, church trips, school outings and conferences.',
                '<p>Thirty seats, air conditioned, with a driver used to managing a full bus and the questions that come with one.</p>
                 <h3>Typical bookings</h3>
                 <ul><li>Daily staff shuttles on a fixed route and timetable</li><li>Church and school trips, including interstate</li><li>Conference and delegate movement between hotel and venue</li><li>Wedding guest transport</li></ul>
                 <h3>Pricing</h3>
                 <p>Within the city we price per day. Interstate we price by route. Recurring staff runs are cheapest on a monthly contract.</p>',
            ],
        ];

        foreach ($services as $i => [$name, $label, $image, $summary, $body]) {
            ServiceType::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'link_label' => $label,
                    'summary' => $summary,
                    'body' => preg_replace('/\s+/', ' ', $body),
                    'image' => $this->media($image),
                    'headline_template' => '{service} in {location}, {city}',
                    'sort_order' => $i,
                    'show_in_directory' => true,
                    'has_landing_page' => true,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function words(): void
    {
        $faqs = [
            ['How much does it cost to rent a car for a day in Lagos or Abuja?', 'It depends on the vehicle. Saloons such as the Camry start around NGN100,000 a day, mid-size SUVs like the Prado sit around NGN170,000, and full-size or luxury vehicles are quoted individually. Every rate on this site includes the driver, fuel within the city and insurance. Airport-only transfers are cheaper than a full day and are shown separately on each vehicle.'],
            ['What is included in the daily rate?', 'The vehicle, a vetted driver, fuel for movement inside the agreed city, and comprehensive insurance. A day runs to ten hours; beyond that an overtime rate applies and we tell you what it is before you book, not afterwards.'],
            ['Do you offer monthly or long-term rentals?', 'Yes, and they are considerably cheaper per day than daily hire. Monthly is the shortest contract term we offer. Quarterly and annual contracts get a dedicated vehicle and driver assigned to your account, with maintenance and replacement cover included.'],
            ['How far in advance should I book?', 'Same-day is usually possible for standard saloons and SUVs if you call early. For weddings, convoys, coaster buses and armoured vehicles, book at least one to two weeks out — and six to eight weeks for a Saturday in December or January.'],
            ['Is fuel included?', 'Fuel is included for movement within the agreed city. Interstate travel is quoted separately by route, with fuel and tolls built into that price. On self-drive hire, fuel is yours: the vehicle goes out full and should come back full.'],
            ['Can I hire a car without a driver?', 'Yes. Self-drive requires a valid Nigerian driver\'s licence held for at least two years, government-issued ID, proof of address and a refundable deposit. There is a short verification call, usually completed the same day.'],
            ['What happens if the vehicle develops a fault mid-hire?', 'We send a replacement. That is the point of running a fleet rather than a single car — you should not lose a day because of our maintenance schedule.'],
            ['Which areas do you deliver to?', 'Anywhere in Lagos, Abuja, Port Harcourt and Ibadan, including both airports in Lagos and Abuja. Our locations directory lists every area we cover. If your address is not on it, ask anyway — it is usually still a yes.'],
            ['How do I pay, and do you take a deposit?', 'Bank transfer for most bookings. Corporate accounts are invoiced monthly. Self-drive requires a refundable deposit; chauffeur-driven hire usually does not. We confirm the full price in writing on WhatsApp before the vehicle moves.'],
            ['Can I extend a booking that has already started?', 'Usually yes, subject to whether the vehicle is committed elsewhere. Tell us as early as you can — an extension asked for at 9am is nearly always possible, one asked for at 6pm often is not.'],
        ];

        foreach ($faqs as $i => [$q, $a]) {
            Faq::updateOrCreate(['question' => $q], ['answer' => $a, 'sort_order' => $i, 'is_active' => true]);
        }

        // Reviews must be real. These are structural placeholders, seeded INACTIVE so
        // nothing invented is ever published: publishing fabricated customer reviews
        // misleads buyers and breaches the FCCPA. Replace the text with quotes from
        // real customers, then tick "Show on the website" for each one.
        $reviews = [
            ['Client name', 'Role or company', 'Paste a real review from a real customer here, then set this record live in the dashboard.', 'Service used'],
            ['Client name 2', 'Role or company', 'Paste a real review from a real customer here, then set this record live in the dashboard.', 'Service used'],
            ['Client name 3', 'Role or company', 'Paste a real review from a real customer here, then set this record live in the dashboard.', 'Service used'],
        ];

        foreach ($reviews as $i => [$name, $role, $quote, $service]) {
            Testimonial::updateOrCreate(
                ['name' => $name],
                ['role' => $role, 'quote' => $quote, 'service' => $service, 'rating' => 5, 'sort_order' => $i, 'reviewed_on' => '', 'is_active' => false]
            );
        }

        $posts = [
            [
                'What it actually costs to hire a car in Lagos in 2026',
                'Pricing', 'sedan-blue.jpg', 8, true,
                'A plain breakdown of daily, airport and monthly rates by vehicle class, plus the four extras that catch people out.',
                '<p>Most quotes you get for car hire in Lagos are not comparable, because different companies include different things. One price includes the driver and fuel, another does not, a third adds a "logistics fee" on the day. Here is how our pricing works and what to check when you are comparing.</p>
                 <h2>Daily rates by vehicle class</h2>
                 <p>A day means ten hours of vehicle and driver time within one city. Saloons such as a Camry run from NGN100,000. Mid-size SUVs — the Prado, the GX 460 — sit between NGN170,000 and NGN200,000. Full-size and luxury vehicles such as the Land Cruiser and LX 570 run from NGN400,000. Buses depend on capacity: a fourteen-seat Hiace is NGN200,000, a thirty-seat Coaster NGN300,000.</p>
                 <h2>Airport transfers are not day rates</h2>
                 <p>A single airport pickup or drop-off is priced separately and is substantially cheaper than a full day — roughly NGN60,000 to NGN70,000 for a saloon and NGN100,000 to NGN120,000 for an SUV. If all you need is a collection from Murtala Muhammed, do not let anyone sell you a full day.</p>
                 <h2>The four things that change the price</h2>
                 <ul>
                   <li><strong>Overtime.</strong> Past ten hours, an hourly rate applies. Ask what it is before you book.</li>
                   <li><strong>Leaving the city.</strong> The moment you cross into another state, city rates stop applying and route pricing starts.</li>
                   <li><strong>Fuel outside the agreed area.</strong> City fuel is included. A run to Ibadan is not city fuel.</li>
                   <li><strong>Waiting on standby.</strong> A vehicle held at your disposal all day costs the same whether it moves or not.</li>
                 </ul>
                 <h2>Where monthly hire beats daily</h2>
                 <p>The arithmetic is simple. If you need a vehicle more than about eight days a month, a monthly contract costs less than paying by the day, and you get a dedicated driver, maintenance cover and a replacement vehicle when yours is being serviced.</p>
                 <h2>What to ask before you pay a deposit</h2>
                 <p>Ask four questions: is the driver included, is fuel included and within what area, what is the overtime rate, and what happens if the vehicle breaks down. Any company that cannot answer those in one message is going to be difficult on the day.</p>',
            ],
            [
                'Prado or Hilux: choosing the right vehicle for site visits',
                'Fleet', 'suv-black.jpg', 6, false,
                'Clearance, comfort and running cost compared for teams working outside the city.',
                '<p>Field teams ask for a Prado and then discover that half their equipment will not fit. Here is how the two actually compare for site work.</p>
                 <h2>Clearance and surface</h2>
                 <p>Both handle bad roads. The Hilux handles worse ones, and it handles them loaded. If your route includes unsurfaced access roads in the rainy season, the pickup is the safer booking.</p>
                 <h2>Carrying equipment</h2>
                 <p>This is usually the deciding factor. The Hilux has an open load bed: generators, cable drums, survey equipment, anything you would rather not put in a cabin. The Prado gives you a sealed boot and seven seats, which is what you want when the cargo is people.</p>
                 <h2>Comfort over distance</h2>
                 <p>On a three-hour highway run, the Prado is a materially better place to sit, and a team that arrives rested works better. For site visits within an hour of base, that matters less than the load bed.</p>
                 <h2>Cost</h2>
                 <p>The Hilux runs about NGN20,000 a day cheaper and uses less fuel. Over a month-long project that is a real number.</p>
                 <h2>The short answer</h2>
                 <p>Carrying equipment or reaching difficult sites: Hilux. Carrying people any distance: Prado. Doing both regularly: book one of each and stop compromising.</p>',
            ],
            [
                'Planning wedding transport that actually runs on time',
                'Events', 'suv-g-class.jpg', 7, false,
                'Convoy sizing, timing buffers and the details that keep a wedding day moving.',
                '<p>Wedding transport fails in predictable ways. Almost all of them are timetable failures rather than vehicle failures.</p>
                 <h2>Size the convoy honestly</h2>
                 <p>Count the people who must be moved, not the people you would like to move. A lead car for the couple, one vehicle for immediate family on each side, and a bus for the bridal party covers most weddings. Guests generally arrange themselves, and planning as though they will not is how budgets get destroyed.</p>
                 <h2>Build in the buffer everyone skips</h2>
                 <p>The gap between the ceremony ending and the reception starting is where days collapse. Photographs always overrun. Add forty-five minutes to whatever your planner has written down, and tell the drivers the padded time, not the optimistic one.</p>
                 <h2>Nominate a contact who is not the couple</h2>
                 <p>Someone has to be reachable from 8am. It cannot be the bride and it should not be the groom. Give the transport company one name and one number, and make sure that person has the full running order.</p>
                 <h2>Brief the drivers on the whole day</h2>
                 <p>A driver who knows he collects the couple at 2pm and where he waits until then will be in position. A driver who only knows the pickup address will be somewhere else when you need him.</p>
                 <h2>Book earlier than feels necessary</h2>
                 <p>December and January Saturdays go first, and they go months out. If you want a specific vehicle for the lead car, six to eight weeks is the minimum.</p>',
            ],
            [
                'A checklist for moving an office over one weekend',
                'Logistics', 'moving-van.jpg', 7, false,
                'Sequencing, packing and access arrangements for a relocation with no Monday downtime.',
                '<p>An office move has one real deadline: people sitting down and working on Monday morning. Everything else is sequencing.</p>
                 <h2>Two weeks out</h2>
                 <ul><li>Confirm lift booking and loading access at both buildings, in writing</li><li>Check whether either building restricts weekend access hours</li><li>Decide who handles IT equipment — your team or ours</li><li>Label every desk and cabinet with its destination room</li></ul>
                 <h2>The week before</h2>
                 <ul><li>Pack everything that is not needed to trade in the final week</li><li>Photograph the back of every server and switch before anything is unplugged</li><li>Confirm parking for the truck at both ends — this is the single most common failure</li></ul>
                 <h2>Moving day sequence</h2>
                 <p>Load in reverse order of need. The things that must be working first — network equipment, the reception desk, the MD\'s office — go on last and come off first. Everything else follows.</p>
                 <h2>Sunday</h2>
                 <p>Reserve Sunday for IT and testing, not for moving furniture. If furniture is still moving on Sunday afternoon, Monday will not work.</p>
                 <h2>What to tell your transport company</h2>
                 <p>Number of rooms, floor at both ends, working lift or not, and whether there is a time window for access. Those four facts determine the crew size and the quote more than the distance does.</p>',
            ],
            [
                'Renewing your Nigerian vehicle documents without the runaround',
                'Compliance', 'interior-console.jpg', 6, false,
                'Which documents you need, what they cost, and which ones are worth handling early.',
                '<p>Every vehicle on Nigerian roads needs the same core set of papers. Missing one turns a routine stop into a long afternoon.</p>
                 <h2>The documents that matter</h2>
                 <ul>
                   <li><strong>Vehicle licence</strong> — renewed annually, the one most often allowed to lapse</li>
                   <li><strong>Insurance certificate</strong> — third-party is the legal minimum; comprehensive is what any hire vehicle should carry</li>
                   <li><strong>Roadworthiness certificate</strong> — annual, requires an inspection</li>
                   <li><strong>Proof of ownership</strong> — issued once, keep the original somewhere safe and a copy in the vehicle</li>
                   <li><strong>Hackney permit</strong> — required if the vehicle carries passengers commercially, which includes hire vehicles</li>
                 </ul>
                 <h2>Renew before expiry, not after</h2>
                 <p>Renewals are straightforward while the document is still valid and become tedious once it has lapsed. Set a calendar reminder six weeks before each expiry date.</p>
                 <h2>If you are hiring rather than owning</h2>
                 <p>This is our problem, not yours. Every vehicle we hire out goes with its full document set, and if you are stopped, the papers are in the car. Ask to see them before you drive away on a self-drive booking — a company that hesitates is telling you something.</p>',
            ],
        ];

        foreach ($posts as $i => [$title, $category, $image, $minutes, $featured, $excerpt, $body]) {
            Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => $category,
                    'excerpt' => $excerpt,
                    'body' => preg_replace('/\s+/', ' ', $body),
                    'cover_image' => $this->media($image),
                    'read_minutes' => $minutes,
                    'author' => 'Editorial desk',
                    'meta_title' => $title,
                    'meta_description' => $excerpt,
                    'is_featured' => $featured,
                    'is_published' => true,
                    'published_at' => now()->subDays($i * 11),
                ]
            );
        }

        $pages = [
            ['Privacy Policy', '<p><em>Last updated: ' . now()->format('F Y') . '. Review this with a lawyer before you rely on it.</em></p>
              <h2>What we collect</h2>
              <p>When you submit a booking request we collect your name, phone number, and optionally your email address, along with the details of the trip you are asking about: dates, pickup and destination, vehicle and any notes you add.</p>
              <h2>Why we collect it</h2>
              <p>To quote and fulfil your booking, to contact you about it, and to keep a record of work we have done. We do not sell your information to anyone.</p>
              <h2>Who sees it</h2>
              <p>Our dispatch staff, and the driver assigned to your booking, who receives only what he needs to complete the job. If you continue the conversation on WhatsApp, that exchange is also governed by WhatsApp\'s own privacy policy.</p>
              <h2>How long we keep it</h2>
              <p>Booking records are retained for accounting and dispute purposes. You may ask us to delete your details at any time by emailing us, and we will do so except where we are required to retain records.</p>
              <h2>Cookies and analytics</h2>
              <p>This site uses cookies necessary for the booking form to function. If analytics are enabled, aggregate visit data may be collected to help us understand which pages are useful.</p>
              <h2>Your rights</h2>
              <p>Under the Nigeria Data Protection Act you may request a copy of the personal data we hold about you, ask us to correct it, or ask us to delete it. Contact us using the details on our contact page.</p>'],
            ['Terms of Service', '<p><em>Last updated: ' . now()->format('F Y') . '. Review this with a lawyer before you rely on it.</em></p>
              <h2>Bookings and quotes</h2>
              <p>A booking is confirmed when we have acknowledged it in writing and any required deposit has been received. Quoted prices hold for the vehicle, dates and area specified in the quote.</p>
              <h2>What a daily rate includes</h2>
              <p>Unless stated otherwise, a daily rate covers the vehicle, the driver, fuel for movement within the agreed city, and comprehensive insurance, for up to ten hours. Time beyond ten hours is charged at the overtime rate quoted at the time of booking. Travel outside the agreed city is priced separately.</p>
              <h2>Self-drive hire</h2>
              <p>Self-drive requires a valid Nigerian driver\'s licence held for at least two years, government-issued identification, proof of address, and a refundable deposit. The vehicle is supplied with a full tank and must be returned with a full tank. The hirer is responsible for traffic offences and fines incurred during the hire period.</p>
              <h2>Cancellation</h2>
              <p>Cancellations made more than 24 hours before the booking start are refunded in full. Within 24 hours, a portion may be retained to cover the driver and vehicle already committed. Cancellations for wedding and event bookings are subject to the terms in the individual quote.</p>
              <h2>Vehicle faults and substitution</h2>
              <p>If a vehicle becomes unavailable or develops a fault, we will supply a replacement of equivalent or higher class at no additional cost. If we cannot, the unused portion of the hire is refunded.</p>
              <h2>Liability</h2>
              <p>Our liability is limited to the value of the booking. We are not liable for consequential losses, including missed flights or appointments, arising from traffic, road closures or other conditions outside our control.</p>
              <h2>Governing law</h2>
              <p>These terms are governed by the laws of the Federal Republic of Nigeria.</p>'],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => Str::slug($data[0])],
                [
                    'title' => $data[0],
                    'body' => preg_replace('/\s+/', ' ', $data[1]),
                    'show_in_footer' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}
