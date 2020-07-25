<?php
declare(strict_types=1);

namespace App\Report;

use GrumpyDictator\FFIIIApiSupport\Exceptions\ApiHttpException;
use GrumpyDictator\FFIIIApiSupport\Model\Tag;
use Illuminate\Support\Collection;
use Log;
/**
 * Class DataCollector
 */
class DataCollector
{
    private Collection $tags;
    /**
     * @throws ApiHttpException
     * @return array
     */
    public function getProducts(): array
    {
        Log::debug('Now in getProducts()');
        $tagCollector = new TagCollector;
        $this->tags       = $tagCollector->collectTags();
        $products     = [];
        // loop all tags, extract product name:
        foreach ($this->tags as $tag) {
            $name = self::getProductName($tag);
            if (null !== $name) {
                $products[] = trim($name);
            }
        }
        $products = array_unique($products);
        Log::debug('Found products', $products);
        return $products;
    }

    /**
     * TODO migrate to trait
     * @param Tag $tag
     *
     * @return string
     */
    public static function getProductName(Tag $tag): ?string
    {
        $parts = explode('-', $tag->tag);

        return $parts[1] ?? null;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }
}
