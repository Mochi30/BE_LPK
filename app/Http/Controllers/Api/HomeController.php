<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\ImpactStat;
use App\Models\Instructor;
use App\Models\Program;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        return [
            'programs' => Program::query()
                ->where('is_active', true)
                ->orderBy('order_index')
                ->limit(6)
                ->get(),
            'instructors' => Instructor::query()
                ->where('is_active', true)
                ->orderBy('order_index')
                ->limit(6)
                ->get(),
            'testimonials' => Testimonial::query()
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('order_index')
                ->limit(6)
                ->get(),
            'articles' => Article::query()
                ->where('is_published', true)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
            'gallery' => GalleryItem::query()
                ->where('is_active', true)
                ->orderBy('order_index')
                ->limit(8)
                ->get(),
            'faqs' => Faq::query()
                ->where('is_active', true)
                ->orderBy('order_index')
                ->limit(8)
                ->get(),
            'impact_stats' => ImpactStat::query()
                ->where('is_active', true)
                ->orderBy('order_index')
                ->limit(6)
                ->get(),
        ];
    }
}
