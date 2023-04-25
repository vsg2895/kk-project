<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Jakten\Services\SitemapService;

/**
 * Class Sitemap
 * @package Jakten\Console\Commands
 */
class Sitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kkj:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build sitemap in public folder.';

    /**
     * @var SitemapService|null $sitemapService
     */
    protected $sitemapService = null;

    /**
     * Sitemap constructor.
     * @param SitemapService $sitemap
     */
    public function __construct(SitemapService $sitemap)
    {
        parent::__construct();
        $this->sitemapService = $sitemap;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->sitemapService->buildSitemap();
    }
}
