<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupportContentService;
use App\Http\Requests\Admin\Content\UpdatePolicyRequest;
use App\Http\Requests\Admin\Content\StoreFaqRequest;
use App\Http\Requests\Admin\Content\UpdateFaqRequest;
use App\Http\Requests\Admin\Content\ReorderFaqsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

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

    public function updatePolicy(UpdatePolicyRequest $request, string $slug): RedirectResponse
    {
        $this->service->updatePolicyBySlug($slug, $request->validated());

        return redirect()
            ->route('admin.policies.index')
            ->with('success', 'Policy updated successfully.');
    }

    public function indexFaqs(Request $request): View
    {
        $faqs = $this->service->getFaqsForAdmin($request->all());

        return view('admin.content.faqs.index', compact('faqs'));
    }

    public function storeFaq(StoreFaqRequest $request): RedirectResponse
    {
        $this->service->createFaq($request->validated());

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function updateFaq(UpdateFaqRequest $request, int $id): RedirectResponse
    {
        $this->service->updateFaq($id, $request->validated());

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
            'message' => 'FAQs reordered successfully'
        ]);
    }
}