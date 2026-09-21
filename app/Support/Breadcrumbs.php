<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Breadcrumbs
{
    /**
     * Derive a breadcrumb trail from the current route name.
     *
     * Convention: a `<resource>.<action>` name whose `<resource>.index` route
     * exists, or a single-segment name. Everything else (settings, auth,
     * two-factor, ...) gets no trail.
     *
     * @return array<int, array{title: string, href: string}>
     */
    public static function forRequest(Request $request): array
    {
        $name = $request->route()?->getName();

        if (! $name) {
            return [];
        }

        if (! str_contains($name, '.')) {
            return [['title' => Str::headline($name), 'href' => $request->url()]];
        }

        [$resource, $action] = explode('.', $name, 2);

        if (! Route::has("{$resource}.index")) {
            return [];
        }

        $title = Str::headline(Str::singular($resource));

        if ($action === 'index') {
            return [['title' => $title, 'href' => $request->url()]];
        }

        return [
            ['title' => $title, 'href' => route("{$resource}.index")],
            ['title' => Str::headline($action), 'href' => $request->url()],
        ];
    }
}
