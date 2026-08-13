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
                'site_name' => 'Your Company Name',
                'tagline' => 'Premium vehicle hire across Nigeria',
                'founded_year' => '2015',
            ],
            'Contact' => [
                'phone' => '+234 000 000 0000',
                'whatsapp_number' => '2340000000000',
                'email' => 'hello@yourdomain.com',
                'address_primary' => 'Your office address, Lagos',
                'address_secondary' => 'Your office address, Abuja',
                'business_hours' => 'Open 24 hours, every day',
            ],
            'Homepage' => [
                'hero_eyebrow' => 'Lagos · Abuja · Port Harcourt',
                'hero_heading' => 'Vehicles, drivers and logistics you can plan around',
                'hero_subheading' => 'Chauffeur-driven cars, self-drive hire, interstate travel, moving trucks and security escorts. Booked in minutes, delivered to your door.',
                'fleet_note' => 'Rates shown are for Lagos and Abuja. For other cities, interstate travel, self-drive or moving trucks, send us a message for a quote.',
                'about_heading' => 'Built for people who cannot afford to be late',
                'about_body' => 'We keep a maintained fleet, vetted drivers and a dispatch desk that answers. Corporate accounts, weddings, airport runs and long-term leases all run on the same standard.',
                'trust_points' => "Confirmed within minutes\nNationwide delivery\nInsurance on every vehicle\n24/7 roadside support\nDaily, weekly and monthly terms\nNo hidden charges",
            ],
            'Numbers' => [
                'stat_years' => '10',
                'stat_clients' => '5,000',
                'stat_vehicles' => '120',
                'stat_rating' => '4.9',
            ],
            'Social' => [
                'facebook_url' => '',
                'instagram_url' => '',
                'twitter_url' => '',
                'tiktok_url' => '',
            ],
            'Search engines' => [
                'meta_title' => 'Car Hire Nigeria | Chauffeur, Self Drive & Moving Trucks',
                'meta_description' => 'Daily, weekly and monthly car hire in Lagos, Abuja and Port Harcourt. SUVs, buses, luxury cars, moving trucks and security escorts.',
                'meta_keywords' => 'car hire nigeria, car rental lagos, car rental abuja, moving truck, self drive, chauffeur service',
            ],
        ];

        foreach ($values as $group => $pairs) {
            foreach ($pairs as $key => $value) {
                Setting::put($key, $value, $group);
            }
        }
    }

    protected function fleet(): void
    {
        $categories = ['SUVs', 'Sedans', 'Buses & Minivans', 'Premium & Luxury', 'Other Services'];
        foreach ($categories as $i => $name) {
            VehicleCategory::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $i]
            );
        }

        $lookup = VehicleCategory::pluck('id', 'slug');

        $vehicles = [
            ['Toyota Prado 2022', 'suvs', 170000, 100000, '7 seats'],
            ['Lexus GX 460', 'suvs', 200000, 120000, '7 seats'],
            ['Toyota Land Cruiser', 'suvs', 400000, 200000, '7 seats'],
            ['Toyota Hilux', 'suvs', 150000, 90000, '5 seats'],
            ['Toyota Camry 2014', 'sedans', 130000, 70000, '4 seats'],
            ['Toyota Camry 2010', 'sedans', 100000, 60000, '4 seats'],
            ['Toyota Sienna 2014', 'buses-minivans', 160000, 100000, '7 seats'],
            ['Toyota Sienna 2006', 'buses-minivans', 140000, 70000, '7 seats'],
            ['Toyota Hiace Bus', 'buses-minivans', 200000, 100000, '14 seats'],
            ['Toyota Coaster Bus', 'buses-minivans', 300000, 200000, '30 seats'],
            ['Mercedes Sprinter', 'buses-minivans', 700000, null, '18 seats'],
            ['Lexus LX 570', 'premium-luxury', 450000, 200000, '7 seats'],
            ['Mercedes G-Class', 'premium-luxury', null, null, '5 seats'],
            ['Armoured B6 SUV', 'premium-luxury', null, null, '5 seats'],
            ['Moving Truck', 'other-services', null, null, null],
            ['Interstate Travel', 'other-services', null, null, null],
            ['Security Escort', 'other-services', null, null, null],
            ['Self Drive Hire', 'other-services', null, null, null],
        ];

        foreach ($vehicles as $i => [$name, $category, $daily, $airport, $seats]) {
            Vehicle::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'vehicle_category_id' => $lookup[$category] ?? null,
                    'daily_rate' => $daily,
                    'secondary_rate' => $airport,
                    'rate_note' => $daily ? null : 'Request a quote',
                    'seats' => $seats,
                    'sort_order' => $i,
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
                'office_address' => 'Victoria Island',
                'airport_branch' => 'Murtala Muhammed International Airport',
                'highlights' => "Corporate fleet management\nIsland to mainland commuting\nWedding car packages\nAirport transfers",
                'locations' => ['Lekki Phase 1', 'Victoria Island', 'Ikeja', 'Ikoyi', 'Ajah', 'Yaba', 'Surulere', 'Gbagada', 'Maryland', 'Festac'],
            ],
            [
                'name' => 'Abuja', 'state' => 'Federal Capital Territory', 'rating' => '4.8/5',
                'tagline' => 'Government, diplomatic and conference transport',
                'areas_summary' => 'Maitama, Wuse, Garki, Asokoro',
                'office_address' => 'Wuse II',
                'airport_branch' => 'Nnamdi Azikiwe International Airport',
                'highlights' => "Diplomatic vehicle services\nConference and event fleets\nExecutive transport\nProtocol vehicles",
                'locations' => ['Wuse 2', 'Maitama', 'Asokoro', 'Garki', 'Gwarinpa', 'Central Business District', 'Utako', 'Jabi', 'Kubwa', 'Wuse Zone 4'],
            ],
            [
                'name' => 'Port Harcourt', 'state' => 'Rivers State', 'rating' => '4.8/5',
                'tagline' => 'Oil and gas site visits, corporate transport',
                'areas_summary' => 'GRA, Trans Amadi, Eliozu',
                'office_address' => 'GRA Phase 2',
                'airport_branch' => 'Port Harcourt International Airport',
                'highlights' => "Site visit vehicles\nAirport transfers\nEvent transportation\nSecurity escorts",
                'locations' => ['GRA Port Harcourt', 'Trans Amadi', 'Eliozu', 'Rumuokoro', 'Woji'],
            ],
            [
                'name' => 'Ibadan', 'state' => 'Oyo State', 'rating' => '4.8/5',
                'tagline' => 'Executive and long-term corporate hire',
                'areas_summary' => 'Bodija, Ring Road, Jericho',
                'office_address' => 'Bodija',
                'airport_branch' => 'Ibadan Airport',
                'highlights' => "Corporate transport\nExecutive travel\nWedding hire\nLong-term lease",
                'locations' => ['Bodija', 'Jericho', 'Ring Road', 'Akobo'],
            ],
        ];

        foreach ($cities as $i => $data) {
            $locations = $data['locations'];
            unset($data['locations']);

            $city = City::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + ['sort_order' => $i, 'is_active' => true]
            );

            foreach ($locations as $j => $area) {
                Location::updateOrCreate(
                    ['slug' => Str::slug($area)],
                    ['city_id' => $city->id, 'name' => $area, 'sort_order' => $j, 'is_active' => true]
                );
            }
        }
    }

    protected function services(): void
    {
        $services = [
            ['Moving Truck', 'Hire Moving Truck', 'Home moves, office relocations and furniture transport with a loading crew.'],
            ['Airport Transfer', 'Book Airport Transfer', 'Flight-tracked pickups and drop-offs with waiting time included.'],
            ['Wedding Car', 'Hire Wedding Car', 'Decorated vehicles, uniformed chauffeurs and convoy arrangements.'],
            ['Corporate Car', 'Hire Corporate Car', 'Monthly and quarterly contracts with a dedicated driver and account manager.'],
            ['Luxury Car', 'Rent Luxury Car', 'Premium sedans and SUVs for guests you need to impress.'],
            ['Security Escort', 'Hire Security Escort', 'Trained escort teams and lead vehicles for sensitive movements.'],
            ['Armoured SUV', 'Hire Armoured SUV', 'B6 and B7 rated vehicles with security-trained drivers.'],
            ['Self Drive', 'Rent Self Drive Car', 'Drive it yourself, with documentation handled and insurance included.'],
            ['Interstate Travel', 'Book Interstate Travel', 'Long-distance road trips with rested, route-familiar drivers.'],
            ['Coaster Bus', 'Hire Coaster Bus', 'Group movement for staff runs, church trips and conferences.'],
        ];

        foreach ($services as $i => [$name, $label, $summary]) {
            ServiceType::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'link_label' => $label,
                    'summary' => $summary,
                    'headline_template' => '{service} in {location}, {city}',
                    'sort_order' => $i,
                    'show_in_directory' => true,
                    'has_landing_page' => $i < 4,
                    'is_active' => true,
                ]
            );
        }
    }

    protected function words(): void
    {
        $faqs = [
            ['How much does it cost to rent a car for a day?', 'Rates depend on the vehicle. Sedans start lower, SUVs sit in the middle, and luxury or armoured vehicles are quoted per request. Every rate on this site includes the driver, fuel within the city and insurance.'],
            ['Do you offer monthly rentals?', 'Yes. Monthly and quarterly contracts carry a significant discount over the daily rate. Send us the vehicle type and duration and we will price it.'],
            ['How far in advance should I book?', 'Same-day bookings are usually possible, but for weddings, convoys and armoured vehicles we recommend at least a week.'],
            ['Is fuel included?', 'Fuel within the agreed city is included. Interstate trips are quoted separately because distance and route conditions vary.'],
            ['Can I hire a car without a driver?', 'Yes, self-drive is available with valid identification, a driver\'s licence and a refundable deposit.'],
            ['What areas do you deliver to?', 'We deliver anywhere in the cities listed on our locations page, including both airports in Lagos and Abuja.'],
        ];

        foreach ($faqs as $i => [$q, $a]) {
            Faq::updateOrCreate(['question' => $q], ['answer' => $a, 'sort_order' => $i, 'is_active' => true]);
        }

        $reviews = [
            ['Tunde A.', 'Business executive', 'Used them for a week of meetings. The driver arrived early every single morning and knew the routes better than my own staff.', 'Executive transport'],
            ['Chioma E.', 'Event planner', 'Three weddings, no surprises. The cars turn up clean, decorated and on time, which is all I need.', 'Wedding transport'],
            ['Peter O.', 'Operations manager', 'We moved our office over a weekend. The truck crew packed, moved and set up before Monday.', 'Office relocation'],
            ['Anita N.', 'Hotel manager', 'Our guests get picked up from the airport without me having to chase anyone. That is worth a lot.', 'Airport transfer'],
            ['Segun A.', 'Field engineer', 'Hilux for site visits, month after month. Maintained properly, and they swap the vehicle if anything goes wrong.', 'Corporate contract'],
        ];

        foreach ($reviews as $i => [$name, $role, $quote, $service]) {
            Testimonial::updateOrCreate(
                ['name' => $name],
                ['role' => $role, 'quote' => $quote, 'service' => $service, 'rating' => 5, 'sort_order' => $i, 'reviewed_on' => 'Recent', 'is_active' => true]
            );
        }

        $posts = [
            ['What it actually costs to hire a car in Lagos', 'Pricing', 'A plain breakdown of daily, weekend and monthly rates by vehicle class, and the extras that catch people out.', true],
            ['Choosing between a Hilux and a Prado for site visits', 'Fleet', 'Ruggedness, comfort and running cost compared for teams working outside the city.', false],
            ['Planning wedding transport that runs on time', 'Events', 'Convoy sizing, timing buffers and the small details that keep a wedding day moving.', false],
            ['A checklist for relocating an office over one weekend', 'Logistics', 'Sequencing, packing and access arrangements for a move with no Monday downtime.', false],
            ['Renewing your vehicle documents without the runaround', 'Compliance', 'What the current process looks like and which documents are worth handling early.', false],
        ];

        foreach ($posts as $i => [$title, $category, $excerpt, $featured]) {
            Post::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'category' => $category,
                    'excerpt' => $excerpt,
                    'body' => "<p>{$excerpt}</p><p>Replace this text with your own article from the dashboard. You can paste formatted text straight in, add headings, lists and links, and upload a cover image.</p>",
                    'read_minutes' => 6,
                    'author' => 'Editorial desk',
                    'is_featured' => $featured,
                    'is_published' => true,
                    'published_at' => now()->subDays($i * 9),
                ]
            );
        }

        $pages = [
            ['Privacy Policy', '<p>Add your privacy policy here from the dashboard.</p>'],
            ['Terms of Service', '<p>Add your terms of service here from the dashboard.</p>'],
        ];

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => Str::slug($data[0])],
                ['title' => $data[0], 'body' => $data[1], 'show_in_footer' => true, 'is_active' => true]
            );
        }
    }
}
