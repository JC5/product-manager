<?php
declare(strict_types=1);

namespace App\Report;

use GrumpyDictator\FFIIIApiSupport\Exceptions\ApiHttpException;
use GrumpyDictator\FFIIIApiSupport\Model\Tag;
use GrumpyDictator\FFIIIApiSupport\Request\GetTagsRequest;
use Illuminate\Support\Collection;
use Log;

/**
 * Class TagCollector
 */
class TagCollector
{
    /**
     * Collect all tags that start with "product-".
     *
     * @throws ApiHttpException
     * @return Collection
     */
    public function collectTags(): Collection
    {
        $request = new GetTagsRequest(config('pm.url'), config('pm.token'));
        $result  = $request->get();
        $tags    = new Collection;
        /** @var Tag $tag */
        foreach ($result as $tag) {
            if (str_starts_with($tag->tag, 'product-')) {
                $tags->push($tag);
                Log::debug(sprintf('Found tag "%s"', $tag->tag));
            }
        }

        return $tags;
    }

}
