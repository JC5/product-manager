<?php
declare(strict_types=1);

namespace App\Report;

use Carbon\Carbon;
use GrumpyDictator\FFIIIApiSupport\Model\Tag;
use GrumpyDictator\FFIIIApiSupport\Model\Transaction;
use GrumpyDictator\FFIIIApiSupport\Model\TransactionGroup;
use GrumpyDictator\FFIIIApiSupport\Request\GetTransactionsByTagRequest;
use Illuminate\Support\Collection;
use Log;

/**
 * Class ReportGenerator
 */
class ReportGenerator
{
    private Collection $tags;

    /**
     * @param array $products
     *
     * @return array
     */
    public function generate(array $products): array
    {
        $return = [];
        /** @var string $product */
        foreach ($products as $product) {
            $return[] = $this->generateReport($product);
        }

        return $return;
    }

    /**
     * @param Collection $tags
     */
    public function setTags(Collection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param array $report
     * @param Tag   $tag
     *
     * @return array
     */
    private function appendTransactions(array $report, Tag $tag): array
    {
        $request = new GetTransactionsByTagRequest(config('pm.url'), config('pm.token'));
        $request->setId($tag->id);
        $tagType = $this->getTagType($tag);
        $result  = $request->get();

        // unify tag types:
        $tagType = $this->unifiedTagType($tagType);
        $oldest = $report['numbers']['purchase_oldest_object'];

        // result can be stored in report no problem:
        foreach ($result as $transaction) {
            $transaction->transactions[0]->date = new Carbon($transaction->transactions[0]->date);
            $report['expenses'][$tagType][]     = $transaction;
        }

        // also get sums:
        /** @var TransactionGroup $transactionGroup */
        foreach ($result as $transactionGroup) {
            /** @var Transaction $transaction */
            foreach ($transactionGroup->transactions as $transaction) {
                $date                                       = new Carbon($transaction->date);
                $oldest                                     = $date->lte($oldest) ? $date : $oldest;
                $report['sums'][$tagType]                   = $report['sums'][$tagType] ?? '0';
                $report['sums'][$tagType]                   = bcadd($report['sums'][$tagType], $transaction->amount);
                $report['numbers']['total_costs_over_time'] = bcadd($report['numbers']['total_costs_over_time'], $transaction->amount);
            }
        }

        $diff                                         = $oldest->diffInMonths(today());
        $report['numbers']['purchase_diff_in_months'] = $diff;
        $report['numbers']['purchase_oldest_date']    = $oldest->format('Y-m-d');
        $report['numbers']['purchase_oldest_object'] = $oldest;
        $report['numbers']['purchase_diff_in_years']  = round($diff / 12, 1);
        $report['numbers']['save_to_replace_now']     = $report['sums']['initial-purchase'];

        if (0 !== $diff) {
            $report['numbers']['save_to_replace_now'] = bcdiv($report['sums']['initial-purchase'], (string) $diff);
        }

        return $report;
    }

    /**
     * @param string $product
     *
     * @return array
     */
    private function generateReport(string $product): array
    {
        Log::debug(sprintf('Now generating report for "%s"', $product));
        $report = $this->getInitialReport($product);
        $tags   = $this->getTagsForProduct($product);

        Log::debug(sprintf('Tags for this product: %d', $tags->count()));

        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $report = $this->appendTransactions($report, $tag);
        }

        return $report;
    }

    /**
     * @param string $product
     *
     * @return array
     */
    private function getInitialReport(string $product): array
    {
        return [
            'product'  => $product,
            'expenses' => [
                'initial-purchase' => [],
            ],
            'sums'     => [
                'initial-purchase' => '0',
            ],
            'numbers'  => [
                'save_to_replace_now'   => '0',
                'total_costs_over_time' => '0',
                'purchase_oldest_object' => today()
            ],
        ];
    }

    /**
     * @param Tag $tag
     *
     * @return string|null
     */
    private function getTagType(Tag $tag): string
    {
        return str_replace(sprintf('product-%s-', DataCollector::getProductName($tag)), '', $tag->tag);
    }

    /**
     * @param string $product
     *
     * @return Collection
     */
    private function getTagsForProduct(string $product): Collection
    {
        Log::debug(sprintf('Know of %d tags', $this->tags->count()));

        return $this->tags->filter(
            static function (Tag $tag) use ($product) {
                return str_starts_with($tag->tag, sprintf('product-%s-', $product));
            }
        );
    }

    /**
     * @param string $tagType
     *
     * @return string|null
     */
    private function unifiedTagType(string $tagType): ?string
    {
        $search = [
            'down-payment'  => 'initial-purchase',
            'startup-costs' => 'initial-purchase',
        ];
        if (isset($search[$tagType])) {
            return $search[$tagType];
        }

        return $tagType;
    }
}
