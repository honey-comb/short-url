<?php
/**
 * @copyright 2018 innovationbase
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

declare(strict_types = 1);

namespace HoneyComb\ShortUrl\Http\Controllers\Admin;

use HoneyComb\Core\Http\Controllers\HCBaseController;
use HoneyComb\Core\Http\Controllers\Traits\HCAdminListHeaders;
use HoneyComb\ShortUrl\Http\Requests\Admin\HCShortUrlRequest;
use HoneyComb\ShortUrl\Models\HCShortUrl;
use HoneyComb\ShortUrl\Services\Admin\HCShortUrlService;
use HoneyComb\Starter\Helpers\HCFrontendResponse;
use Illuminate\Database\Connection;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class HCShortUrlController
 * @package HoneyComb\ShortUrl\Http\Controllers\Admin
 */
class HCShortUrlController extends HCBaseController
{
    use HCAdminListHeaders;

    /**
     * @var HCShortUrlService
     */
    protected $service;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var HCFrontendResponse
     */
    private $response;

    /**
     * HCShortUrlController constructor.
     * @param Connection $connection
     * @param HCFrontendResponse $response
     * @param HCShortUrlService $service
     */
    public function __construct(Connection $connection, HCFrontendResponse $response, HCShortUrlService $service)
    {
        $this->connection = $connection;
        $this->response = $response;
        $this->service = $service;
    }

    /**
     * Admin panel page view
     *
     * @return View
     */
    public function index(): View
    {
        $config = [
            'title' => trans('HCShortUrl::short_urls.page_title'),
            'url' => route('admin.api.short.urls'),
            'form' => route('admin.api.form-manager', ['short.urls']),
            'headers' => $this->getTableColumns(),
            'actions' => $this->getActions('honey_comb_short_url_short_urls_admin'),
        ];

        return view('HCCore::admin.service.index', ['config' => $config]);
    }

    /**
     * Get admin page table columns settings
     *
     * @return array
     */
    public function getTableColumns(): array
    {
        $columns = [
            'url' => $this->headerText(trans('HCShortUrl::short_urls.url')),
            'key' => $this->headerText(trans('HCShortUrl::short_urls.key')),
            'clicks' => $this->headerText(trans('HCShortUrl::short_urls.clicks')),
        ];

        return $columns;
    }

    /**
     * @param string $id
     * @return HCShortUrl|null
     */
    public function getById(string $id): ?HCShortUrl
    {
        return $this->service->getRepository()->findOrFail($id);
    }

    /**
     * Creating data list
     * @param HCShortUrlRequest $request
     * @return JsonResponse
     */
    public function getListPaginate(HCShortUrlRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getListPaginate($request)
        );
    }

    /**
     * Create data list
     * @param HCShortUrlRequest $request
     * @return JsonResponse
     */
    public function getOptions(HCShortUrlRequest $request): JsonResponse
    {
        return response()->json(
            $this->service->getRepository()->getOptions($request)
        );
    }

    /**
     * Create record
     *
     * @param HCShortUrlRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(HCShortUrlRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $record = $this->service->generate(
                $request->input('url'),
                (int)$request->input('code_length')
            );

            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            report($exception);

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Created', $record);
    }

    /**
     * Update record
     *
     * @param HCShortUrlRequest $request
     * @param string $recordId
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(HCShortUrlRequest $request, string $recordId): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $record = $this->service->getRepository()->findOrFail($recordId);
            $record->update($request->getRecordData());

            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            report($exception);

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Updated');
    }

    /**
     * Soft delete record
     *
     * @param HCShortUrlRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function deleteSoft(HCShortUrlRequest $request): JsonResponse
    {
        $this->connection->beginTransaction();

        try {
            $this->service->getRepository()->deleteSoft($request->getListIds());

            $this->connection->commit();
        } catch (\Throwable $exception) {
            $this->connection->rollBack();

            report($exception);

            return $this->response->error($exception->getMessage());
        }

        return $this->response->success('Successfully deleted');
    }

}
