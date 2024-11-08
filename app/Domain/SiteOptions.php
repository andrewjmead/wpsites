<?php

namespace App\Domain;

use Illuminate\Support\Collection;

class SiteOptions
{
    /**
     * Convert a collection of sites into a collection of site options for laravel prompts.
     *
     * @param Collection<Site> $sites
     *
     * @return Collection<SiteOption>
     */
    public static function from(Collection $sites): Collection
    {
        return $sites->map(function (Site $site) {
            return new SiteOption($site);
        });
    }
}
