<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\Policy;
use App\Repositories\Contracts\SupportContentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class SupportContentService
{
    protected SupportContentRepositoryInterface $repository;

    public function __construct(SupportContentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    // ========================================================================
    // POLICIES MANAGEMENT
    // ========================================================================

    public function getAllPolicies(): Collection
    {
        return $this->repository->getPolicies();
    }

    public function updatePolicyBySlug(string $slug, array $data): Policy
    {
        $policy = $this->repository->findPolicyBySlug($slug);

        if (!$policy) {
            abort(404, 'Policy not found');
        }

        $updatedPolicy = $this->repository->updatePolicy($policy, $data);

        Cache::forget("policy_{$slug}"); 
        Cache::tags(['content_policies'])->flush();

        return $updatedPolicy;
    }

    // ========================================================================
    // FAQ MANAGEMENT
    // ========================================================================

    public function getFaqsForAdmin(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getFaqs($filters, 15); 
    }

    public function createFaq(array $data): Faq
    {
        $faq = $this->repository->createFaq($data);
        
        $this->clearFaqCache();
        
        return $faq;
    }

    public function updateFaq(int $id, array $data): Faq
    {
        $faq = $this->repository->findFaqById($id);

        if (!$faq) {
            abort(404, 'FAQ not found');
        }

        $updatedFaq = $this->repository->updateFaq($faq, $data);

        $this->clearFaqCache();

        return $updatedFaq;
    }

    public function deleteFaq(int $id): bool
    {
        $faq = $this->repository->findFaqById($id);

        if (!$faq) {
            abort(404, 'FAQ not found');
        }

        $result = $this->repository->deleteFaq($faq);

        $this->clearFaqCache();

        return $result;
    }

    public function reorderFaqs(array $order): void
    {
        $this->repository->updateFaqOrder($order);
        
        $this->clearFaqCache();
    }

    protected function clearFaqCache(): void
    {
        Cache::tags(['content_faqs'])->flush();
    }
}