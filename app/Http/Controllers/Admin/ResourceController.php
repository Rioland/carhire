<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * One controller for every content type in config/admin.php.
 * The list screen, form, validation and image uploads are all generated
 * from that config, so adding a section never means writing a controller.
 */
class ResourceController extends Controller
{
    public function index(Request $request, string $resource)
    {
        $config = $this->config($resource);
        $query = $config['model']::query();

        if (! empty($config['with'])) {
            $query->with($config['with']);
        }

        foreach ($config['columns'] as $key => $column) {
            if (($column['type'] ?? null) === 'count') {
                $query->withCount(Str::before($key, '_count'));
            }
        }

        if ($term = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($config, $term) {
                foreach ($config['search'] ?? [] as $field) {
                    $q->orWhere($field, 'like', "%{$term}%");
                }
            });
        }

        [$orderColumn, $orderDirection] = $config['order'] ?? ['id', 'desc'];
        $records = $query->orderBy($orderColumn, $orderDirection)->paginate(20)->withQueryString();

        return view('admin.resource.index', compact('resource', 'config', 'records') + ['term' => $term]);
    }

    public function create(string $resource)
    {
        $config = $this->config($resource);
        $record = new $config['model'];

        foreach ($config['fields'] as $name => $field) {
            if (array_key_exists('default', $field)) {
                $record->{$name} = $field['default'];
            }
        }

        return view('admin.resource.form', compact('resource', 'config', 'record'));
    }

    public function store(Request $request, string $resource)
    {
        $config = $this->config($resource);
        $record = new $config['model'];

        $this->fill($request, $record, $config);
        $record->save();

        return redirect()
            ->route('admin.resource.index', $resource)
            ->with('status', $config['singular'] . ' created.');
    }

    public function edit(string $resource, int $id)
    {
        $config = $this->config($resource);
        $record = $config['model']::findOrFail($id);

        return view('admin.resource.form', compact('resource', 'config', 'record'));
    }

    public function update(Request $request, string $resource, int $id)
    {
        $config = $this->config($resource);
        $record = $config['model']::findOrFail($id);

        $this->fill($request, $record, $config);
        $record->save();

        return redirect()
            ->route('admin.resource.index', $resource)
            ->with('status', $config['singular'] . ' saved.');
    }

    public function destroy(string $resource, int $id)
    {
        $config = $this->config($resource);
        $record = $config['model']::findOrFail($id);
        $record->delete();

        return redirect()
            ->route('admin.resource.index', $resource)
            ->with('status', $config['singular'] . ' deleted.');
    }

    /** Validate the request and copy every configured field onto the model. */
    protected function fill(Request $request, Model $record, array $config): void
    {
        $rules = [];
        foreach ($config['fields'] as $name => $field) {
            if (! empty($field['rules'])) {
                $rules[$name] = $field['rules'];
            }
            if ($field['type'] === 'image') {
                $rules[$name] = 'nullable|image|max:4096';
            }
        }

        $validated = $request->validate($rules);

        foreach ($config['fields'] as $name => $field) {
            switch ($field['type']) {
                case 'image':
                    if ($request->hasFile($name)) {
                        if ($record->{$name}) {
                            Storage::disk('public')->delete($record->{$name});
                        }
                        $record->{$name} = $request->file($name)->store('uploads', 'public');
                    } elseif ($request->boolean('remove_' . $name)) {
                        if ($record->{$name}) {
                            Storage::disk('public')->delete($record->{$name});
                        }
                        $record->{$name} = null;
                    }
                    break;

                case 'boolean':
                    $record->{$name} = $request->boolean($name);
                    break;

                case 'slug':
                    $source = $validated[$name] ?? $request->input($name);
                    if (blank($source)) {
                        $source = $request->input($field['from'] ?? 'name');
                    }
                    $record->{$name} = $this->uniqueSlug($record, $name, Str::slug($source));
                    break;

                case 'number':
                case 'money':
                    $value = $request->input($name);
                    $record->{$name} = ($value === null || $value === '') ? null : (int) preg_replace('/\D+/', '', $value);
                    break;

                default:
                    if ($request->has($name)) {
                        $record->{$name} = $request->input($name);
                    }
            }
        }
    }

    protected function uniqueSlug(Model $record, string $column, string $slug): string
    {
        $slug = $slug ?: Str::random(8);
        $base = $slug;
        $i = 2;

        while ($record->newQuery()
            ->where($column, $slug)
            ->when($record->exists, fn ($q) => $q->where($record->getKeyName(), '!=', $record->getKey()))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected function config(string $resource): array
    {
        $config = config('admin.' . $resource);
        abort_if(! $config, 404, 'Unknown section.');

        return $config;
    }
}
