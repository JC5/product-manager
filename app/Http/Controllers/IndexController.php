<?php
declare(strict_types=1);

namespace App\Http\Controllers;


use App\Report\DataCollector;
use App\Report\ReportGenerator;
use GrumpyDictator\FFIIIApiSupport\Model\Tag;

/**
 * Class IndexController
 */
class IndexController extends Controller
{

    /**
     *
     * @throws \GrumpyDictator\FFIIIApiSupport\Exceptions\ApiHttpException
     */
    public function index()
    {
        // collect all the user's tags:
        $collector = new DataCollector;
        $products  = $collector->getProducts();

        $reportGenerator = new ReportGenerator();
        $reportGenerator->setTags($collector->getTags());
        $reports = $reportGenerator->generate($products);
//        echo '<pre>';
//        print_r($reports);
//        exit;


        return view('index', compact('reports'));
    }



}
