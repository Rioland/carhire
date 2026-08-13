<?php

/*
|--------------------------------------------------------------------------
| Admin resources
|--------------------------------------------------------------------------
| Every section of the dashboard is described here. Adding a new editable
| content type means adding a model + migration and one entry below — the
| list screen, the form, validation and file uploads are all generated.
|
| Field types: text, textarea, editor, number, money, image, select,
|              relation, boolean, date, slug, email, url
*/

return [

    'vehicles' => [
        'label' => 'Fleet',
        'singular' => 'Vehicle',
        'icon' => 'car',
        'model' => App\Models\Vehicle::class,
        'order' => ['sort_order', 'asc'],
        'search' => ['name'],
        'columns' => [
            'image' => ['label' => '', 'type' => 'thumb'],
            'name' => ['label' => 'Vehicle'],
            'category.name' => ['label' => 'Category'],
            'daily_rate' => ['label' => 'Day rate', 'type' => 'money'],
            'secondary_rate' => ['label' => 'Airport', 'type' => 'money'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'name' => ['label' => 'Vehicle name', 'type' => 'text', 'rules' => 'required|string|max:120', 'hint' => 'e.g. Toyota Prado 2022'],
            'slug' => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'name', 'rules' => 'nullable|string|max:140'],
            'vehicle_category_id' => ['label' => 'Category', 'type' => 'relation', 'model' => App\Models\VehicleCategory::class, 'display' => 'name', 'rules' => 'nullable|exists:vehicle_categories,id'],
            'image' => ['label' => 'Photo', 'type' => 'image', 'hint' => 'Landscape works best. Around 1200x800.'],
            'daily_rate' => ['label' => 'Day rate (NGN)', 'type' => 'money', 'rules' => 'nullable|integer|min:0', 'width' => 'half'],
            'daily_label' => ['label' => 'Day rate label', 'type' => 'text', 'rules' => 'nullable|max:40', 'width' => 'half', 'default' => 'Per day'],
            'secondary_rate' => ['label' => 'Second rate (NGN)', 'type' => 'money', 'rules' => 'nullable|integer|min:0', 'width' => 'half'],
            'secondary_label' => ['label' => 'Second rate label', 'type' => 'text', 'rules' => 'nullable|max:40', 'width' => 'half', 'default' => 'Airport'],
            'rate_note' => ['label' => 'Price note', 'type' => 'text', 'rules' => 'nullable|max:120', 'hint' => 'Shown instead of a price when you leave the day rate empty. e.g. "Contact us for a quote"'],
            'seats' => ['label' => 'Seats', 'type' => 'text', 'rules' => 'nullable|max:40', 'width' => 'half'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer', 'width' => 'half', 'hint' => 'Lower numbers show first.'],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'rules' => 'nullable|string'],
            'is_featured' => ['label' => 'Feature on the homepage hero', 'type' => 'boolean'],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
    ],

    'vehicle-categories' => [
        'label' => 'Fleet categories',
        'singular' => 'Category',
        'icon' => 'tag',
        'model' => App\Models\VehicleCategory::class,
        'order' => ['sort_order', 'asc'],
        'search' => ['name'],
        'columns' => [
            'name' => ['label' => 'Category'],
            'slug' => ['label' => 'Slug'],
            'sort_order' => ['label' => 'Position'],
        ],
        'fields' => [
            'name' => ['label' => 'Name', 'type' => 'text', 'rules' => 'required|max:80', 'hint' => 'Becomes a filter button on the fleet grid.'],
            'slug' => ['label' => 'Slug', 'type' => 'slug', 'from' => 'name', 'rules' => 'nullable|max:80'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer'],
        ],
    ],

    'cities' => [
        'label' => 'Cities',
        'singular' => 'City',
        'icon' => 'map',
        'model' => App\Models\City::class,
        'order' => ['sort_order', 'asc'],
        'search' => ['name', 'state'],
        'columns' => [
            'name' => ['label' => 'City'],
            'state' => ['label' => 'State'],
            'locations_count' => ['label' => 'Areas', 'type' => 'count'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'name' => ['label' => 'City', 'type' => 'text', 'rules' => 'required|max:80', 'width' => 'half'],
            'state' => ['label' => 'State', 'type' => 'text', 'rules' => 'nullable|max:80', 'width' => 'half'],
            'slug' => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'name', 'rules' => 'nullable|max:80', 'hint' => 'The page will live at /car-rental-{slug}'],
            'tagline' => ['label' => 'Tagline', 'type' => 'text', 'rules' => 'nullable|max:160'],
            'areas_summary' => ['label' => 'Areas covered', 'type' => 'text', 'rules' => 'nullable|max:200', 'hint' => 'Short list shown on the city card. e.g. Victoria Island, Lekki, Ikeja, Ikoyi'],
            'rating' => ['label' => 'Rating badge', 'type' => 'text', 'rules' => 'nullable|max:20', 'width' => 'half', 'hint' => 'e.g. 4.9/5'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer', 'width' => 'half'],
            'intro' => ['label' => 'Intro paragraph', 'type' => 'textarea', 'rules' => 'nullable|string'],
            'highlights' => ['label' => 'Service highlights', 'type' => 'textarea', 'rules' => 'nullable|string', 'hint' => 'One per line. These become the bullet list on the city card.'],
            'office_address' => ['label' => 'Main office', 'type' => 'text', 'rules' => 'nullable|max:160', 'width' => 'half'],
            'airport_branch' => ['label' => 'Airport branch', 'type' => 'text', 'rules' => 'nullable|max:160', 'width' => 'half'],
            'hero_image' => ['label' => 'Header image', 'type' => 'image'],
            'meta_title' => ['label' => 'SEO title', 'type' => 'text', 'rules' => 'nullable|max:180'],
            'meta_description' => ['label' => 'SEO description', 'type' => 'textarea', 'rules' => 'nullable|max:300'],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
    ],

    'locations' => [
        'label' => 'Areas',
        'singular' => 'Area',
        'icon' => 'pin',
        'model' => App\Models\Location::class,
        'with' => ['city'],
        'order' => ['sort_order', 'asc'],
        'search' => ['name'],
        'columns' => [
            'name' => ['label' => 'Area'],
            'city.name' => ['label' => 'City'],
            'slug' => ['label' => 'Slug'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'city_id' => ['label' => 'City', 'type' => 'relation', 'model' => App\Models\City::class, 'display' => 'name', 'rules' => 'required|exists:cities,id'],
            'name' => ['label' => 'Area name', 'type' => 'text', 'rules' => 'required|max:120', 'hint' => 'e.g. Lekki Phase 1'],
            'slug' => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'name', 'rules' => 'nullable|max:140'],
            'landmarks' => ['label' => 'Nearby landmarks', 'type' => 'text', 'rules' => 'nullable|max:200', 'hint' => 'Used in the page copy for local relevance.'],
            'blurb' => ['label' => 'Area description', 'type' => 'textarea', 'rules' => 'nullable|string'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer'],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
        'note' => 'Every area is automatically combined with every service below to build its own landing page. Adding one area here creates a full set of new pages.',
    ],

    'service-types' => [
        'label' => 'Services',
        'singular' => 'Service',
        'icon' => 'grid',
        'model' => App\Models\ServiceType::class,
        'order' => ['sort_order', 'asc'],
        'search' => ['name'],
        'columns' => [
            'name' => ['label' => 'Service'],
            'slug' => ['label' => 'Slug'],
            'show_in_directory' => ['label' => 'In directory', 'type' => 'boolean'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'name' => ['label' => 'Service name', 'type' => 'text', 'rules' => 'required|max:120', 'hint' => 'e.g. Moving Truck'],
            'slug' => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'name', 'rules' => 'nullable|max:140'],
            'link_label' => ['label' => 'Directory link text', 'type' => 'text', 'rules' => 'nullable|max:120', 'hint' => 'e.g. Hire Moving Truck'],
            'headline_template' => ['label' => 'Page headline pattern', 'type' => 'text', 'rules' => 'nullable|max:200', 'hint' => 'Use {service}, {location} and {city}. e.g. "{service} in {location}, {city}"'],
            'summary' => ['label' => 'Short summary', 'type' => 'textarea', 'rules' => 'nullable|string'],
            'body' => ['label' => 'Full page content', 'type' => 'editor', 'rules' => 'nullable|string'],
            'image' => ['label' => 'Image', 'type' => 'image'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer'],
            'show_in_directory' => ['label' => 'Include in the location directory', 'type' => 'boolean', 'default' => true],
            'has_landing_page' => ['label' => 'Give it a standalone service page', 'type' => 'boolean', 'default' => true],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
    ],

    'posts' => [
        'label' => 'Blog',
        'singular' => 'Article',
        'icon' => 'file',
        'model' => App\Models\Post::class,
        'order' => ['published_at', 'desc'],
        'search' => ['title', 'category'],
        'columns' => [
            'cover_image' => ['label' => '', 'type' => 'thumb'],
            'title' => ['label' => 'Title'],
            'category' => ['label' => 'Category'],
            'published_at' => ['label' => 'Published', 'type' => 'date'],
            'is_published' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'title' => ['label' => 'Title', 'type' => 'text', 'rules' => 'required|max:200'],
            'slug' => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'title', 'rules' => 'nullable|max:220', 'hint' => 'The article lives at /articles/{slug}'],
            'category' => ['label' => 'Category', 'type' => 'text', 'rules' => 'nullable|max:80', 'width' => 'half', 'hint' => 'Becomes a filter button on the blog.'],
            'read_minutes' => ['label' => 'Read time (minutes)', 'type' => 'number', 'rules' => 'nullable|integer|min:1', 'width' => 'half'],
            'cover_image' => ['label' => 'Cover image', 'type' => 'image'],
            'excerpt' => ['label' => 'Excerpt', 'type' => 'textarea', 'rules' => 'nullable|string', 'hint' => 'Shown on the blog listing card.'],
            'body' => ['label' => 'Article', 'type' => 'editor', 'rules' => 'nullable|string'],
            'author' => ['label' => 'Author', 'type' => 'text', 'rules' => 'nullable|max:120', 'width' => 'half'],
            'published_at' => ['label' => 'Publish date', 'type' => 'date', 'rules' => 'nullable|date', 'width' => 'half'],
            'meta_title' => ['label' => 'SEO title', 'type' => 'text', 'rules' => 'nullable|max:180'],
            'meta_description' => ['label' => 'SEO description', 'type' => 'textarea', 'rules' => 'nullable|max:300'],
            'is_featured' => ['label' => 'Pin to the top of the blog', 'type' => 'boolean'],
            'is_published' => ['label' => 'Published', 'type' => 'boolean', 'default' => true],
        ],
    ],

    'testimonials' => [
        'label' => 'Reviews',
        'singular' => 'Review',
        'icon' => 'star',
        'model' => App\Models\Testimonial::class,
        'order' => ['sort_order', 'asc'],
        'search' => ['name'],
        'columns' => [
            'name' => ['label' => 'Client'],
            'role' => ['label' => 'Role'],
            'rating' => ['label' => 'Stars'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'name' => ['label' => 'Client name', 'type' => 'text', 'rules' => 'required|max:120', 'width' => 'half'],
            'role' => ['label' => 'Role or company', 'type' => 'text', 'rules' => 'nullable|max:120', 'width' => 'half'],
            'quote' => ['label' => 'Review', 'type' => 'textarea', 'rules' => 'required|string'],
            'rating' => ['label' => 'Stars', 'type' => 'number', 'rules' => 'nullable|integer|min:1|max:5', 'width' => 'half', 'default' => 5],
            'service' => ['label' => 'Service used', 'type' => 'text', 'rules' => 'nullable|max:120', 'width' => 'half'],
            'reviewed_on' => ['label' => 'Date shown', 'type' => 'text', 'rules' => 'nullable|max:60', 'hint' => 'Free text, e.g. March 2026'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer'],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
    ],

    'faqs' => [
        'label' => 'FAQs',
        'singular' => 'Question',
        'icon' => 'help',
        'model' => App\Models\Faq::class,
        'order' => ['sort_order', 'asc'],
        'search' => ['question'],
        'columns' => [
            'question' => ['label' => 'Question'],
            'sort_order' => ['label' => 'Position'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'question' => ['label' => 'Question', 'type' => 'text', 'rules' => 'required|max:250'],
            'answer' => ['label' => 'Answer', 'type' => 'textarea', 'rules' => 'required|string'],
            'sort_order' => ['label' => 'Position', 'type' => 'number', 'rules' => 'nullable|integer'],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
    ],

    'pages' => [
        'label' => 'Pages',
        'singular' => 'Page',
        'icon' => 'doc',
        'model' => App\Models\Page::class,
        'order' => ['title', 'asc'],
        'search' => ['title'],
        'columns' => [
            'title' => ['label' => 'Page'],
            'slug' => ['label' => 'URL'],
            'is_active' => ['label' => 'Live', 'type' => 'boolean'],
        ],
        'fields' => [
            'title' => ['label' => 'Title', 'type' => 'text', 'rules' => 'required|max:180'],
            'slug' => ['label' => 'URL slug', 'type' => 'slug', 'from' => 'title', 'rules' => 'nullable|max:180'],
            'body' => ['label' => 'Content', 'type' => 'editor', 'rules' => 'nullable|string'],
            'meta_title' => ['label' => 'SEO title', 'type' => 'text', 'rules' => 'nullable|max:180'],
            'meta_description' => ['label' => 'SEO description', 'type' => 'textarea', 'rules' => 'nullable|max:300'],
            'show_in_footer' => ['label' => 'Link from the footer', 'type' => 'boolean', 'default' => true],
            'is_active' => ['label' => 'Show on the website', 'type' => 'boolean', 'default' => true],
        ],
    ],

];
