<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Content\ReorderFaqsRequest;
use App\Http\Requests\Admin\Content\StoreFaqRequest;
use App\Http\Requests\Admin\Content\StorePolicyRequest;
use App\Http\Requests\Admin\Content\UpdateFaqRequest;
use App\Http\Requests\Admin\Content\UpdatePolicyRequest;
use App\Models\Policy;
use App\Services\SupportContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportContentController extends Controller
{
    protected SupportContentService $service;

    public function __construct(SupportContentService $service)
    {
        $this->service = $service;
    }

    public function indexPolicies(): View
    {
        $policies = $this->service->getAllPolicies();
        return view('admin.content.policies.index', compact('policies'));
    }

    public function storePolicy(StorePolicyRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->createPolicy($data);

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Policy created successfully.');
    }

    public function updatePolicy(UpdatePolicyRequest $request, string $slug): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $policy = $this->service->updatePolicyBySlug($slug, $data);

        return redirect()
            ->route('policy.show', $policy->slug)
            ->with('success', 'Policy updated successfully.');
    }

    public function destroyPolicy(string $slug): RedirectResponse
    {
        $this->service->deletePolicy($slug);

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Policy deleted successfully.');
    }

    public function indexFaqs(Request $request): View
    {
        $faqs = $this->service->getFaqsForAdmin($request->all());
        return view('admin.content.faqs.index', compact('faqs'));
    }

    public function storeFaq(StoreFaqRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->createFaq($data);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function updateFaq(UpdateFaqRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->updateFaq($id, $data);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroyFaq(int $id): RedirectResponse
    {
        $this->service->deleteFaq($id);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }

    public function reorderFaqs(ReorderFaqsRequest $request): JsonResponse
    {
        $this->service->reorderFaqs($request->validated()['order']);

        return response()->json([
            'status' => 'success',
            'message' => 'FAQs reordered successfully',
        ]);
    }

    public function showPolicy(string $slug): View
    {
        $query = Policy::where('slug', $slug);

        if (! auth()->check() || auth()->user()->user_type !== 'admin') {
            $query->where('is_active', true);
        }

        $policy = $query->firstOrFail();

        return view('admin.content.policies.show', compact('policy'));
    }
}