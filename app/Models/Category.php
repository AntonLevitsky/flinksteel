<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function allProducts()
    {
        $childIds = $this->children()->pluck('id');
        return Product::where('category_id', $this->id)
            ->orWhereIn('category_id', $childIds);
    }

    /**
     * Map of finish/grade variants and shape siblings worth cross-linking from a
     * category's intro text. Keyed by current slug → list of [slug, label] pairs
     * shown to the buyer when the current category isn't the right finish or shape.
     */
    private const CROSS_LINK_MAP = [
        // Stabstahl ↔ Edelstahl variants
        'rundstahl' => [['edelstahl-rundstahl', 'Rundstahl in Edelstahl']],
        'edelstahl-rundstahl' => [['rundstahl', 'Rundstahl in Baustahl (S235/S355)']],
        'flachstahl' => [['edelstahl-flachstahl', 'Flachstahl in Edelstahl']],
        'edelstahl-flachstahl' => [['flachstahl', 'Flachstahl in Baustahl']],
        'vierkantstahl' => [['rundstahl', 'Rundstahl'], ['sechskantstahl', 'Sechskantstahl']],
        'sechskantstahl' => [['rundstahl', 'Rundstahl'], ['vierkantstahl', 'Vierkantstahl']],

        // Profilstahl ↔ Edelstahl Profile + sibling profiles
        'ipe-traeger' => [['hea-traeger', 'HEA-Träger'], ['heb-traeger', 'HEB-Träger']],
        'hea-traeger' => [['heb-traeger', 'HEB-Träger'], ['ipe-traeger', 'IPE-Träger']],
        'heb-traeger' => [['hea-traeger', 'HEA-Träger'], ['ipe-traeger', 'IPE-Träger']],
        'upn' => [['t-stahl', 'T-Stahl'], ['winkelstahl', 'Winkelstahl']],
        't-stahl' => [['upn', 'U-Stahl (UPN)'], ['winkelstahl', 'Winkelstahl']],
        'winkelstahl' => [['upn', 'U-Stahl (UPN)'], ['t-stahl', 'T-Stahl']],

        // Bleche ↔ Edelstahlbleche
        'feinbleche' => [['grobbleche', 'Grobbleche'], ['edelstahlbleche', 'Bleche in Edelstahl']],
        'grobbleche' => [['feinbleche', 'Feinbleche'], ['edelstahlbleche', 'Bleche in Edelstahl']],
        'traenenbleche' => [['feinbleche', 'Feinbleche'], ['edelstahlbleche', 'Edelstahlbleche']],
        'edelstahlbleche' => [['feinbleche', 'Feinbleche'], ['grobbleche', 'Grobbleche']],

        // Rohre — shape siblings
        'rundrohre' => [['quadratrohre', 'Quadratrohre'], ['rechteckrohre', 'Rechteckrohre']],
        'quadratrohre' => [['rundrohre', 'Rundrohre'], ['rechteckrohre', 'Rechteckrohre']],
        'rechteckrohre' => [['rundrohre', 'Rundrohre'], ['quadratrohre', 'Quadratrohre']],
        'praezisionsstahlrohre' => [['rundrohre', 'Rundrohre'], ['quadratrohre', 'Quadratrohre']],

        // NE-Metalle siblings
        'aluminium' => [['messing', 'Messing'], ['kupfer', 'Kupfer']],
        'messing' => [['aluminium', 'Aluminium'], ['kupfer', 'Kupfer']],
        'kupfer' => [['aluminium', 'Aluminium'], ['messing', 'Messing']],
    ];

    /**
     * Short intro paragraph for category pages. Falls back to a generic line.
     */
    private const CATEGORY_INTRO = [
        'rundstahl' => 'Rundstahl in den Standardgüten S235JR und S355J2 ab Lager — gezogen oder warmgewalzt, mit Zuschnitt nach Ihrem Maß.',
        'edelstahl-rundstahl' => 'Rundstahl in Edelstahl 1.4301 und 1.4571 — geschliffen oder schwarz, mit Zeugnissen nach EN 10204 3.1.',
        'flachstahl' => 'Warmgewalzter Flachstahl S235JR ab Lager — von 12×5 mm bis 200×30 mm, mit Zuschnitt und Anarbeitung.',
        'edelstahl-flachstahl' => 'Flachstahl in Edelstahl 1.4301 und 1.4571 — kaltgezogen, mit Zeugnissen nach EN 10204 3.1 verfügbar.',
        'vierkantstahl' => 'Vierkantstahl S235JR und S355J2 — gezogen oder warmgewalzt, mit Zuschnitt nach Maß.',
        'sechskantstahl' => 'Sechskantstahl S235JR und S355J2 für Schraubenfertigung und Sonderverbindungen.',
        'ipe-traeger' => 'IPE-Träger nach DIN 1025-5 ab Lager — Längen bis 12 m, kürzbar nach Bedarf.',
        'hea-traeger' => 'HEA-Träger nach DIN 1025-3 — Standardprofile mit hoher Tragfähigkeit, ab Lager und auf Bestellung.',
        'heb-traeger' => 'HEB-Träger nach DIN 1025-2 — schwere Stützen- und Trägerprofile für tragende Konstruktionen.',
        'upn' => 'U-Stahl (UPN) nach DIN 1026-1 in Standardgüten S235JR und S355J2.',
        't-stahl' => 'T-Stahl nach DIN EN 10055 — gewalzte Profile für Rahmen- und Stützenkonstruktionen.',
        'winkelstahl' => 'Winkelstahl gleichschenklig nach DIN EN 10056 — von 20×3 mm bis 200×20 mm ab Lager.',
        'feinbleche' => 'Feinbleche bis 3 mm Dicke — kaltgewalzt oder warmgewalzt, mit Zuschnitt nach Maß.',
        'grobbleche' => 'Grobbleche ab 3 mm Dicke — warmgewalzt nach EN 10025, mit Brennschnitt nach Ihrem Maß.',
        'traenenbleche' => 'Tränenbleche aus S235JR — als Boden-, Stufen- und Verkleidungsblech mit rutschhemmender Oberfläche.',
        'edelstahlbleche' => 'Bleche aus Edelstahl 1.4301 / 1.4571 — geschliffen, gebürstet oder mit Folierung.',
        'rundrohre' => 'Stahl-Rundrohre nach DIN EN 10210/10219 — geschweißt oder nahtlos, mit Zuschnitt nach Maß.',
        'quadratrohre' => 'Quadratrohre nach DIN EN 10210/10219 — Konstruktionsrohre für Stahl- und Maschinenbau.',
        'rechteckrohre' => 'Rechteckrohre nach DIN EN 10210/10219 — kaltgefertigt oder warmgewalzt, mit Zuschnittservice.',
        'praezisionsstahlrohre' => 'Präzisionsstahlrohre nach DIN EN 10305 — kaltgezogen, mit engen Toleranzen.',
        'aluminium' => 'Aluminium-Halbzeuge — Profile, Bleche und Rundmaterial in EN AW-6060 und EN AW-6082.',
        'messing' => 'Messing CuZn39Pb3 / CuZn37 — Stangen, Rohre und Bleche für Zerspanung und Konstruktion.',
        'kupfer' => 'Kupfer Cu-DHP / Cu-ETP — Bleche, Stangen und Rohre für Elektro- und Sanitäranwendungen.',
    ];

    public function getRelatedCategories(): array
    {
        $links = self::CROSS_LINK_MAP[$this->slug] ?? [];
        $slugs = array_column($links, 0);
        $found = self::whereIn('slug', $slugs)->get()->keyBy('slug');

        $result = [];
        foreach ($links as [$slug, $label]) {
            if ($found->has($slug)) {
                $result[] = ['category' => $found[$slug], 'label' => $label];
            }
        }
        return $result;
    }

    public function getIntroText(): ?string
    {
        return self::CATEGORY_INTRO[$this->slug] ?? null;
    }
}
