<?php

namespace App\Domain;

readonly class SiteOption
{
    public function __construct(private Site $site)
    {

    }

    /**
     * Laravel Prompts will attempt to convert the object into a string to calculate the label
     * to show for the option. This method is where the label can be customized.
     *
     * @return string
     */
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
