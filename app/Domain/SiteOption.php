<?php

namespace App\Domain;

readonly class SiteOption
{
    public function __construct(private Site $site)
    {

    }

    public function __toString(): string
    {
        if (ConfigFile::parse()->get_site_directories()->count() > 1) {
            return $this->site->directory();
        }

        return $this->site->slug();
    }

    public function site(): Site
    {
        return $this->site;
    }
}
