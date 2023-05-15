<?php

declare(strict_types=1);

namespace App\Controller\Marketing;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Foundation\Annotation\Explanation;
use App\Model\Marketing\Brand;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Brand controller
 * Class BrandController
 * @Controller(prefix="marketing/brand_module/brand")
 */
class BrandController extends AbstractController
{
    /**
     * @Inject()
     * @var Brand
     */
    private $brand;

    /**
     * Get the brand list
     * @RequestMapping(path="list", methods="get")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index()
    {
        $brandQuery = $this->brand->newQuery();

        $total = $brandQuery->count();
        $brandQuery = $this->pagingCondition($brandQuery, $this->request->all());
        //Determine whether there are query conditions
        if (!empty($this->request->input('name'))) $brandQuery->where('name', 'like', '%' . $this->request->input('name') . '%');
        $list = $brandQuery->get();

        return $this->success([
            'list' => $list,
            'total' => $total,
        ]);
    }

    /**
     * @Explanation(content="Add brand operation")
     * @RequestMapping(path="store", methods="post")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function store()
    {
        $postData = $this->request->all();
        $params = [
            'brand_name' => isset($postData['brand_name']) ?? '',
            'from_name' => isset($postData['from_name']) ?? '',
            'from_email' => isset($postData['from_email']) ?? '',
            'reply_to_email' => isset($postData['reply_to_email']) ?? '',
            'brand_logo' => isset($postData['brand_logo']) ?? '',
            'smtp_provider' => isset($postData['smtp_provider']) ?? '',
            'smtp_host' => isset($postData['smtp_host']) ?? '',
            'smtp_port' => isset($postData['smtp_port']) ?? '',
            'smtp_ssl' => isset($postData['smtp_ssl']) ?? '',
            'smtp_username' => isset($postData['smtp_username']) ?? '',
            'smtp_password' => isset($postData['smtp_password']) ?? '',
            'custom_domain_protocol' => isset($postData['custom_domain_protocol']) ?? '',
            'custom_domain' => isset($postData['custom_domain']) ?? '',
            'enable_custom_domain' => isset($postData['enable_custom_domain']) ?? '',
            'recaptcha_sitekey' => isset($postData['recaptcha_sitekey']) ?? '',
            'recaptcha_secret_key' => isset($postData['recaptcha_secret_key']) ?? '',
            'gdpr_options' => isset($postData['gdpr_options']) ?? '',
            'opens_tracking' => isset($postData['opens_tracking']) ?? '',
            'clicks_tracking' => isset($postData['clicks_tracking']) ?? '',
            'query_string' => isset($postData['query_string']) ?? '',
            'test_email_prefix' => isset($postData['test_email_prefix']) ?? '',
            'allowed_attachments' => isset($postData['allowed_attachments']) ?? '',
            'sort_by' => isset($postData['sort_by']) ?? '',
            'brand_report_rows' => isset($postData['brand_report_rows']) ?? '',
            'hide_hidden_lists' => isset($postData['hide_hidden_lists']) ?? '',
            'login_email' => isset($postData['login_email']) ?? '',
            'generate_password' => isset($postData['generate_password']) ?? '',
            'language' => isset($postData['language']) ?? '',
            'client_privileges' => isset($postData['client_privileges']) ?? '',
            'notify_campaign_sent' => isset($postData['notify_campaign_sent']) ?? '',
            'currency' => isset($postData['aaaa']) ?? '',
            'delivery_fee' => isset($postData['delivery_fee']) ?? '',
            'cost_per_recipient' => isset($postData['cost_per_recipient']) ?? '',
            'choose_limit' => isset($postData['choose_limit']) ?? '',
            'monthly_limit' => isset($postData['monthly_limit']) ?? '',
            'current_limit' => isset($postData['current_limit']) ?? '',
            'reset_on_day' => isset($postData['reset_on_day']) ?? '',
        ];
        //Configuration verification
        $rules = [
            'brand_name' => 'required',
            'from_name' => 'required',
            'from_email' => 'required',
            'reply_to_email' => 'required',
        ];
        $message = [
            'brand_name.required' => '[brand_name] required',
            'from_name.required' => '[from_name] required',
            'from_email.required' => '[from_email] required',
            'reply_to_email.required' => '[reply_to_email] required',
        ];
        $this->verifyParams($params, $rules, $message);

        if (!Brand::create($params)) $this->throwExp(400, 'Add brand failure');

        return $this->successByMessage('Successful brand');
    }

    /**
     * Get the data of a single brand
     * @param int $id
     * @RequestMapping(path="edit/{id}", methods="get")
     * @Middleware(RequestMiddleware::class)
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(int $id)
    {
        $brandInfo = Brand::findById($id);
        if (empty($brandInfo)) $this->throwExp(StatusCode::ERR_VALIDATION, 'Failure to get brand information');

        return $this->success([
            'list' => $brandInfo
        ]);
    }

    /**
     * @Explanation(content="Modify the brand operation")
     * @param int $id
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(int $id)
    {
        $postData = $this->request->all();
        $params = [
            'id' => $id,
            'brand_name' => isset($postData['brand_name']) ?? '',
            'from_name' => isset($postData['from_name']) ?? '',
            'from_email' => isset($postData['from_email']) ?? '',
            'reply_to_email' => isset($postData['reply_to_email']) ?? '',
            'brand_logo' => isset($postData['brand_logo']) ?? '',
            'smtp_provider' => isset($postData['smtp_provider']) ?? '',
            'smtp_host' => isset($postData['smtp_host']) ?? '',
            'smtp_port' => isset($postData['smtp_port']) ?? '',
            'smtp_ssl' => isset($postData['smtp_ssl']) ?? '',
            'smtp_username' => isset($postData['smtp_username']) ?? '',
            'smtp_password' => isset($postData['smtp_password']) ?? '',
            'custom_domain_protocol' => isset($postData['custom_domain_protocol']) ?? '',
            'custom_domain' => isset($postData['custom_domain']) ?? '',
            'enable_custom_domain' => isset($postData['enable_custom_domain']) ?? '',
            'recaptcha_sitekey' => isset($postData['recaptcha_sitekey']) ?? '',
            'recaptcha_secret_key' => isset($postData['recaptcha_secret_key']) ?? '',
            'gdpr_options' => isset($postData['gdpr_options']) ?? '',
            'opens_tracking' => isset($postData['opens_tracking']) ?? '',
            'clicks_tracking' => isset($postData['clicks_tracking']) ?? '',
            'query_string' => isset($postData['query_string']) ?? '',
            'test_email_prefix' => isset($postData['test_email_prefix']) ?? '',
            'allowed_attachments' => isset($postData['allowed_attachments']) ?? '',
            'sort_by' => isset($postData['sort_by']) ?? '',
            'brand_report_rows' => isset($postData['brand_report_rows']) ?? '',
            'hide_hidden_lists' => isset($postData['hide_hidden_lists']) ?? '',
            'login_email' => isset($postData['login_email']) ?? '',
            'generate_password' => isset($postData['generate_password']) ?? '',
            'language' => isset($postData['language']) ?? '',
            'client_privileges' => isset($postData['client_privileges']) ?? '',
            'notify_campaign_sent' => isset($postData['notify_campaign_sent']) ?? '',
            'currency' => isset($postData['aaaa']) ?? '',
            'delivery_fee' => isset($postData['delivery_fee']) ?? '',
            'cost_per_recipient' => isset($postData['cost_per_recipient']) ?? '',
            'choose_limit' => isset($postData['choose_limit']) ?? '',
            'monthly_limit' => isset($postData['monthly_limit']) ?? '',
            'current_limit' => isset($postData['current_limit']) ?? '',
            'reset_on_day' => isset($postData['reset_on_day']) ?? '',
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
            'brand_name' => 'required',
            'from_name' => 'required',
            'from_email' => 'required',
            'reply_to_email' => 'required',
        ];
        $message = [
            'id.required' => 'Id is invalid',
            'brand_name.required' => '[brand_name] required',
            'from_name.required' => '[from_name] required',
            'from_email.required' => '[from_email] required',
            'reply_to_email.required' => '[reply_to_email] required',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!Brand::query()->where('id', $id)->update($params)) $this->throwExp(400, 'Updating brand failed');

        return $this->successByMessage('Email Brand is updated successfully');
    }

    /**
     * @Explanation(content="Delete character operation")
     * @param int $id
     * @RequestMapping(path="destroy/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(RequestMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function destroy(int $id)
    {
        $params = [
            'id' => $id,
        ];
        //Configuration verification
        $rules = [
            'id' => 'required',
        ];
        $message = [
            'id.required' => 'Parameter is invalid',
        ];

        $this->verifyParams($params, $rules, $message);

        if (!Brand::query()->where('id', $id)->delete()) $this->throwExp(400, 'Delete email brand failure');

        return $this->successByMessage('Email brand is deleted  successfully');
    }

}
