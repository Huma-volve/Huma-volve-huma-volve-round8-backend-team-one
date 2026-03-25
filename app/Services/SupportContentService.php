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

    public function getAllPolicies(): Collection
    {
        return $this->repository->getPolicies();
    }

    public function createPolicy(array $data): Policy
    {
        $policy = $this->repository->createPolicy($data);
        $this->clearPolicyCache();
        return $policy;
    }

    public function updatePolicyBySlug(string $slug, array $data): Policy
    {
        $policy = $this->repository->findPolicyBySlug($slug);

        if (!$policy) {
            abort(404, 'Policy not found');
        }

        $updatedPolicy = $this->repository->updatePolicy($policy, $data);
        $this->clearPolicyCache();
        return $updatedPolicy;
    }

    public function deletePolicy(string $slug): bool
    {
        $policy = $this->repository->findPolicyBySlug($slug);

        if (!$policy) {
            abort(404, 'Policy not found');
        }

        $result = $this->repository->deletePolicy($policy);
        $this->clearPolicyCache();
        return $result;
    }

    public function getActivePoliciesForApi(): Collection
    {
        return Cache::remember('policies_public', 60 * 60 * 24, function () {
            return $this->repository->getActivePolicies();
        });
    }

    protected function clearPolicyCache(): void
    {
        Cache::forget('policies_public');
    }

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

    public function getActiveFaqsForApi(): Collection
    {
        return Cache::remember('faqs_public', 60 * 60 * 24, function () {
            return $this->repository->getActiveFaqs();
        });
    }

    protected function clearFaqCache(): void
    {
        Cache::forget('faqs_admin');
        Cache::forget('faqs_public');
    }
}