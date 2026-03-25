<?php

namespace App\Repositories\Contracts;

use App\Models\Faq;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupportContentRepositoryInterface
{
    // Policies
    public function getPolicies(array $filters = []): Collection;
    public function getActivePolicies(): Collection;
    public function findPolicyBySlug(string $slug): ?Policy;
    public function createPolicy(array $data): Policy;
    public function updatePolicy(Policy $policy, array $data): Policy;
    public function deletePolicy(Policy $policy): bool;

    // FAQs
    public function getFaqs(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getActiveFaqs(): Collection;
    public function findFaqById(int $id): ?Faq;
    public function createFaq(array $data): Faq;
    public function updateFaq(Faq $faq, array $data): Faq;
    public function deleteFaq(Faq $faq): bool;
    public function updateFaqOrder(array $order): void;
}
