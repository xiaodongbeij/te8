<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Scf\V20180416\Models;
use TencentCloud\Common\AbstractModel;

/**
 * TerminateAsyncEvent请求参数结构体
 *
 * @method string getFunctionName() 获取函数名称
 * @method void setFunctionName(string $FunctionName) 设置函数名称
 * @method string getInvokeRequestId() 获取终止的调用请求id
 * @method void setInvokeRequestId(string $InvokeRequestId) 设置终止的调用请求id
 * @method string getNamespace() 获取命名空间
 * @method void setNamespace(string $Namespace) 设置命名空间
 */
class TerminateAsyncEventRequest extends AbstractModel
{
    /**
     * @var string 函数名称
     */
    public $FunctionName;

    /**
     * @var string 终止的调用请求id
     */
    public $InvokeRequestId;

    /**
     * @var string 命名空间
     */
    public $Namespace;

    /**
     * @param string $FunctionName 函数名称
     * @param string $InvokeRequestId 终止的调用请求id
     * @param string $Namespace 命名空间
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("FunctionName",$param) and $param["FunctionName"] !== null) {
            $this->FunctionName = $param["FunctionName"];
        }

        if (array_key_exists("InvokeRequestId",$param) and $param["InvokeRequestId"] !== null) {
            $this->InvokeRequestId = $param["InvokeRequestId"];
        }

        if (array_key_exists("Namespace",$param) and $param["Namespace"] !== null) {
            $this->Namespace = $param["Namespace"];
        }
    }
}