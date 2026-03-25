<?php

namespace App\Repositories;

use App\Models\Faq;
use App\Models\Policy;
use App\Repositories\Contracts\SupportContentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SupportContentRepository implements SupportContentRepositoryInterface
{
    public function getPolicies(array $filters = []): Collection // get all policies only Admin use
    {
        return Policy::query()
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('title->en', 'like', '%'.$filters['search'].'%')
                    ->orWhere('title->ar', 'like', '%'.$filters['search'].'%');
            })->get();
    }

    public function getActivePolicies(): Collection // get active policies only Mobile API
    {
        // cache could be added here later
        return Policy::active()->select(['slug', 'title', 'content'])->get(); // select only what mobile needs
    }

    public function findPolicyBySlug(string $slug): ?Policy // find policy by slug for editing or viewing
    {
        return Policy::where('slug', $slug)->first();
    }

    public function createPolicy(array $data): Policy
    {
        return Policy::create($data);
    }

    public function updatePolicy(Policy $policy, array $data): Policy // update policy content
    {
        $policy->update($data);

        return $policy->refresh();
    }

    public function deletePolicy(Policy $policy): bool
    {
        return $policy->delete();
    }

    // ========================================================================
    // FAQ METHODS
    // ========================================================================

    public function getFaqs(array $filters = [], int $perPage = 15): LengthAwarePaginator // get paginated FAQs for Admin Table
    {
        return Faq::query()
            ->sorted()
            ->when(isset($filters['search']), function ($query) use ($filters) {
                $query->where('question->en', 'like', '%'.$filters['search'].'%')
                    ->orWhere('question->ar', 'like', '%'.$filters['search'].'%');
            })->paginate($perPage);
    }

    public function getActiveFaqs(): Collection // get active FAQs only Mobile API
    {
        return Faq::active()
            ->sorted()
            ->select(['id', 'question', 'answer'])->get();
    }

    public function findFaqById(int $id): ?Faq // find FAQ by ID for editing or viewing
    {
        return Faq::find($id);
    }

    public function createFaq(array $data): Faq
    {
        return Faq::create($data);
    }

    public function updateFaq(Faq $faq, array $data): Faq
    {
        $faq->update($data);

        return $faq->refresh();
    }

    public function deleteFaq(Faq $faq): bool
    {
        return $faq->delete();
    }

    public function updateFaqOrder(array $order): void
    {
        // use transaction to ensure all updates succeed or fail together
        DB::transaction(function () use ($order) {
            foreach ($order as $item) {
                Faq::where('id', $item['id'])->update(['sort_order' => $item['order']]);
            }
        });
    }
}
