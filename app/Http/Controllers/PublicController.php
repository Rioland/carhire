<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Faq;
use App\Models\Location;
use App\Models\Page;
use App\Models\Post;
use App\Models\ServiceType;
use App\Models\Testimonial;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        return view('public.home', [
            'categories' => VehicleCategory::orderBy('sort_order')->get(),
            'vehicles' => Vehicle::active()->with('category')->orderBy('sort_order')->get(),
            'services' => ServiceType::active()->where('has_landing_page', true)->orderBy('sort_order')->get(),
            'cities' => City::active()->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::active()->orderBy('sort_order')->get(),
            'faqs' => Faq::active()->orderBy('sort_order')->get(),
            'posts' => Post::published()->orderByDesc('published_at')->limit(4)->get(),
        ]);
    }

    public function blog(Request $request)
    {
        $query = Post::published();

        if ($term = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $posts = $query->orderByDesc('published_at')->paginate(12)->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
            'featured' => Post::published()->where('is_featured', true)->orderByDesc('published_at')->first(),
            'categories' => Post::published()->whereNotNull('category')->distinct()->orderBy('category')->pluck('category'),
            'activeCategory' => $category,
            'term' => $request->query('q'),
        ]);
    }

    public function article(string $slug)
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->category, fn ($q) => $q->where('category', $post->category))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        if ($related->count() < 3) {
            $related = $related->concat(
                Post::published()->where('id', '!=', $post->id)
                    ->whereNotIn('id', $related->pluck('id'))
                    ->orderByDesc('published_at')
                    ->limit(3 - $related->count())->get()
            );
        }

        return view('blog.show', compact('post', 'related'));
    }

    public function directory()
    {
        $cities = City::active()
            ->with(['locations' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $services = ServiceType::active()->where('show_in_directory', true)->orderBy('sort_order')->get();

        return view('locations.directory', compact('cities', 'services'));
    }

    /** City landing page: /car-rental-lagos */
    public function city(string $slug)
    {
        $city = City::active()->where('slug', $slug)
            ->with(['locations' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->firstOrFail();

        return view('locations.city', [
            'city' => $city,
            'services' => ServiceType::active()->where('show_in_directory', true)->orderBy('sort_order')->get(),
            'vehicles' => Vehicle::active()->orderBy('sort_order')->limit(8)->get(),
            'testimonials' => Testimonial::active()->orderBy('sort_order')->limit(3)->get(),
        ]);
    }

    /**
     * Programmatic page: /locations/{service-slug}-{location-slug}
     * e.g. /locations/moving-truck-lekki-phase-1
     */
    public function locationService(string $slug)
    {
        $service = null;
        $location = null;

        foreach (ServiceType::active()->orderByRaw('LENGTH(slug) DESC')->get() as $candidate) {
            $prefix = $candidate->slug . '-';
            if (str_starts_with($slug, $prefix)) {
                $rest = substr($slug, strlen($prefix));
                $match = Location::active()->where('slug', $rest)->with('city')->first();
                if ($match) {
                    $service = $candidate;
                    $location = $match;
                    break;
                }
            }
        }

        abort_if(! $service || ! $location, 404);

        $siblings = ServiceType::active()->where('show_in_directory', true)
            ->where('id', '!=', $service->id)->orderBy('sort_order')->limit(6)->get();

        $nearby = Location::active()->where('city_id', $location->city_id)
            ->where('id', '!=', $location->id)->orderBy('sort_order')->limit(8)->get();

        return view('locations.service', [
            'service' => $service,
            'location' => $location,
            'city' => $location->city,
            'siblings' => $siblings,
            'nearby' => $nearby,
            'vehicles' => Vehicle::active()->orderBy('sort_order')->limit(6)->get(),
            'faqs' => Faq::active()->orderBy('sort_order')->limit(5)->get(),
        ]);
    }

    /** Standalone service page: /services/moving-truck */
    public function service(string $slug)
    {
        $service = ServiceType::active()->where('has_landing_page', true)->where('slug', $slug)->firstOrFail();

        return view('locations.service-landing', [
            'service' => $service,
            'cities' => City::active()->with(['locations' => fn ($q) => $q->where('is_active', true)])->orderBy('sort_order')->get(),
            'vehicles' => Vehicle::active()->orderBy('sort_order')->limit(6)->get(),
            'faqs' => Faq::active()->orderBy('sort_order')->limit(6)->get(),
        ]);
    }

    public function contact()
    {
        return view('public.contact', [
            'cities' => City::active()->orderBy('sort_order')->get(),
            'vehicles' => Vehicle::active()->orderBy('sort_order')->get(),
        ]);
    }

    public function page(string $slug)
    {
        $page = Page::active()->where('slug', $slug)->firstOrFail();

        return view('public.page', compact('page'));
    }

    public function sitemap()
    {
        $urls = [route('home'), route('blog'), route('directory'), route('contact')];

        foreach (City::active()->get() as $city) {
            $urls[] = route('city', $city->slug);
        }
        foreach (ServiceType::active()->where('has_landing_page', true)->get() as $service) {
            $urls[] = route('service', $service->slug);
        }
        foreach (Post::published()->get() as $post) {
            $urls[] = route('article', $post->slug);
        }
        foreach (Page::active()->get() as $page) {
            $urls[] = route('page', $page->slug);
        }

        $services = ServiceType::active()->where('show_in_directory', true)->get();
        foreach (Location::active()->get() as $location) {
            foreach ($services as $service) {
                $urls[] = route('location.service', $service->slug . '-' . $location->slug);
            }
        }

        return response()->view('public.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
