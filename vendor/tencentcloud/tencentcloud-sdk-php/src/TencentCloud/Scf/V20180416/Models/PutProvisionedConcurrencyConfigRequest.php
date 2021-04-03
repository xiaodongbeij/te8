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
 * PutProvisionedConcurrencyConfig请求参数结构体
 *
 * @method string getFunctionName() 获取需要设置预置并发的函数的名称
 * @method void setFunctionName(string $FunctionName) 设置需要设置预置并发的函数的名称
 * @method string getQualifier() 获取函数的版本号，注：$LATEST版本不支持预置并发
 * @method void setQualifier(string $Qualifier) 设置函数的版本号，注：$LATEST版本不支持预置并发
 * @method integer getVersionProvisionedConcurrencyNum() 获取预置并发数量，注：所有版本的预置并发数总和存在上限限制，当前的上限是：函数最大并发配额 - 100
 * @method void setVersionProvisionedConcurrencyNum(integer $VersionProvisionedConcurrencyNum) 设置预置并发数量，注：所有版本的预置并发数总和存在上限限制，当前的上限是：函数最大并发配额 - 100
 * @method string getNamespace() 获取函数所属命名空间，默认为default
 * @method void setNamespace(string $Namespace) 设置函数所属命名空间，默认为default
 */
class PutProvisionedConcurrencyConfigRequest extends AbstractModel
{
    /**
     * @var string 需要设置预置并发的函数的名称
     */
    public $FunctionName;

    /**
     * @var string 函数的版本号，注：$LATEST版本不支持预置并发
     */
    public $Qualifier;

    /**
     * @var integer 预置并发数量，注：所有版本的预置并发数总和存在上限限制，当前的上限是：函数最大并发配额 - 100
     */
    public $VersionProvisionedConcurrencyNum;

    /**
     * @var string 函数所属命名空间，默认为default
     */
    public $Namespace;

    /**
     * @param string $FunctionName 需要设置预置并发的函数的名称
     * @param string $Qualifier 函数的版本号，注：$LATEST版本不支持预置并发
     * @param integer $VersionProvisionedConcurrencyNum 预置并发数量，注：所有版本的预置并发数总和存在上限限制，当前的上限是：函数最大并发配额 - 100
     * @param string $Namespace 函数所属命名空间，默认为default
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

        if (array_key_exists("Qualifier",$param) and $param["Qualifier"] !== null) {
            $this->Qualifier = $param["Qualifier"];
        }

        if (array_key_exists("VersionProvisionedConcurrencyNum",$param) and $param["VersionProvisionedConcurrencyNum"] !== null) {
            $this->VersionProvisionedConcurrencyNum = $param["VersionProvisionedConcurrencyNum"];
        }

        if (array_key_exists("Namespace",$param) and $param["Namespace"] !== null) {
            $this->Namespace = $param["Namespace"];
        }
    }
}
