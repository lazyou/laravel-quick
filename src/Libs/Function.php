<?php

declare(strict_types=1);

if (! function_exists('vueSearch')) {
    /**
     * 模型搜索通用方法 (列表数据).
     * @param $model
     * @param array $fieldMaps eg: [ 'uid', 'status' => 'like', 'ids' => 'in' ] // 默认uid的值处理为 equal
     * @param string $sortField 默认排序字段
     * @param string $sortType 默认排序类型
     * @throws Exception
     * @return mixed
     */
    function vueSearch($model, $fieldMaps = [], $sortField = 'created_at', $sortType = 'DESC')
    {
        if (is_string($model)) {
            $model = new $model();
        }

        // 字段搜索
        // 对每个字段进行搜索, 字段值为 空字符串 则跳过
        foreach ($fieldMaps as $field => $type) {
            if (is_int($field)) {
                $field = $type;
                $type = 'equal';
            }

            $value = request($field, '');
            if ($value === '') {
                continue;
            }

            switch ($type) {
                case 'equal' :
                    $model = $model->where($field, $value);
                    break;
                case 'in' :
                    $model = $model->whereIn($field, $value);
                    break;
                case 'like' :
                    $model = $model->where($field, 'like', "%{$value}%");
                    break;
                case 'between':
                    // 数组要传递 Y-m-d H:i:s 格式, 单一日期仅需要 Y-m-d格式(自动补充到今天)
                    if (is_array($value)) {
                        $startDate = $value[0];
                        $endDate = $value[1];
                    } else {
                        $startDate = "{$value} 00:00:00";
                        $endDate = "{$value} 23:59:59";
                    }

                    $model = $model->whereBetween($field, [$startDate, $endDate]);
                    break;
                default:
                    throw new Exception("未知查询类型: {$type}");
            }
        }

        // 字段排序
        $sort = request('sort', '');
        if (! empty($sort)) {
            $sortField = substr($sort, 1);
            $sortType = substr($sort, 0, 1) === '+' ? 'ASC' : 'DESC';
        }

        return $model->orderBy($sortField, $sortType);
    }
}

/*
 * 分页用
 */
if (! function_exists('vuePaginate')) {
    function vuePaginate($model, $fieldMaps = [], $sortField = 'created_at', $sortType = 'DESC')
    {
        $page = request('limit', 10);

        return vueSearch($model, $fieldMaps, $sortField, $sortType)->paginate($page);
    }
}

/*
 * 构建tree
 */
if (! function_exists('buildTree')) {
    function buildTree(array $elements, $parentId = 0)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}

if (! function_exists('arrayToTree')) {
    /**
     * 数组转树.
     * @param $arr     源数组，所有数据都必须可以追溯到根节点 0，孤立的节点数据将被忽略
     * @return array
     */
    function arrayToTree($arr)
    {
        $build_tree = function (&$list, $parent) use (&$build_tree) {
            $tree = [];

            foreach ($parent as $item) {
                $item['children'] = [];
                if (isset($list[$item['id']])) {
                    $item['children'] = $build_tree($list, $list[$item['id']]);
                }

                $tree[] = $item;
            }

            return $tree;
        };

        $newArr = [];
        $newArr[0] = []; // 用来放 parent_id 为 0 的节点

        // 以 parent_id 作为数组的 key 重组
        foreach ($arr as $item) {
            $newArr[$item['parent_id']][] = $item;
        }

        $root = $newArr[0];

        // 从 root
        return $build_tree($newArr, $root);
    }
}

if (! function_exists('treeEachNode')) {
    /**
     * 遍历tree节点以及子节点.
     *
     * @param array $nodes
     * @param callable $callback
     */
    function treeEachNode($nodes, $callback)
    {
        foreach ($nodes as $node) {
            $callback($node);
            if (! empty($node['children'])) {
                treeEachNode($node['children'], $callback);
            }
        }
    }
}

if (! function_exists('arrayPick')) {
    /**
     * 数组提取 by key name.
     *
     * @param array $input
     * @param array $columns
     * @return array
     * @example
     *   $arrayPick(['a' => 1, 'b' => 2, 'c' => 3], ['a', 'c']);
     *   // => ['a' => 1, 'c' => 3]
     */
    function arrayPick($input, $columns)
    {
        return array_intersect_key($input, array_flip($columns));
    }
}

if (! function_exists('nowAt')) {
    function nowAt()
    {
        return date('Y-m-d H:i:s');
    }
}

/*
 * 权限验证： 模板专用
 */
if (! function_exists('p')) {
    function p($as)
    {
        return permission($as);
    }
}

/*
 * 权限验证： 不传参则验证当前路由
 */
if (! function_exists('permission')) {
    function permission($as = null)
    {
        if (is_null($as)) {
            $as = \Illuminate\Support\Facades\Route::currentRouteName();
        }

        return Illuminate\Support\Facades\Auth::user()->hasPermission($as);
    }
}

if (! function_exists('getRand')) {
    /**
     * 抽奖方法： TODO: 似乎有点问题 https://www.cnblogs.com/tinyphp/p/3513459.html.
     * @param $proArr， key是奖品id，value是奖品中将概率
     * Array
     * (
     * [137] => 100000
     * [138] => 1
     * [139] => 1
     * [140] => 9999
     * )
     * @return int|string
     */
    function getRand($proArr)
    {
        // 概率数组的总概率精度
        $proSum = array_sum($proArr);
        $randNum = mt_rand(1, $proSum);

        // 概率数组循环
        foreach ($proArr as $key => $proCur) {
            $proSum = $proSum - $proCur;

            if ($randNum > $proSum) {
                return $key;
            }
        }
    }
}

/*
 * 快速匹配
 * eg: pregMatchQuick($body, "/<title>(.*?)<\/title>/")
 */
if (! function_exists('pregMatchQuick')) {
    function pregMatchQuick($content, $pattern, $default = null)
    {
        $one = $default;
        $result = preg_match($pattern, $content, $matches);
        if ($result && count($matches) == 2) {
            $one = trim($matches[1]);
        }

        return $one;
    }
}
