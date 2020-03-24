<?php

namespace Dngjnh\LaravelUtility\Traits;

trait ModelResourceTrait
{
    /**
     * 获取列表
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $where id 或 where 数组。数组格式： [
     *                                                      ['a', '=', 'b'],
     *                                                  ]
     * @param array $columns
     * @param array $joins 格式：   [
     *                                  'type',
     *                                  'table',
     *                                  callable,
     *                              ]
     * @param array $withs
     * @param array $orders
     * @param array $groups
     * @return object
     */
    public function scopeForList($query, $where = [], $columns = ['*'], $joins = [], $withs = [], $orders = [], $groups = [])
    {
        $this->applyLocalScopes($query);

        $model = $query->getModel();

        if (!empty($withs)) {
            $query->with($withs);
        }

        $query->select($columns);

        foreach ($joins as $joinArr) {
            $query->{$joinArr[0]}($joinArr[1], $joinArr[2]);
        }

        if (is_array($where)) {
            $where = $this->getFilterFromRequest($query, $where);
            $query->where($where);
        } else {
            $query->where($model->getQualifiedKeyName(), '=', $where);
        }

        if (empty($orders)) {
            $query->orderBy($model->getQualifiedKeyName(), 'desc');
        } else {
            foreach ($orders as $ok => $order) {
                $query->orderBy($order[0], $order[1]);
            }
        }

        if (!empty($groups)) {
            $query->groupBy($groups);
        }

        if (!is_array($where) || empty(request('page'))) {
            return $query->get();
        } else {
            return $query->paginate();
        }
    }

    /**
     * 应用本地 scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder &$query
     */
    protected function applyLocalScopes(&$query)
    {
        //
    }

    /**
     * 从请求参数中获取字段筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $where
     * @return array
     */
    protected function getFilterFromRequest($query, $where)
    {
        $tableName = $this->getTable();

        $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($tableName);

        // 获取请求参数
        foreach (request()->input() as $rk => $rv) {
            if (in_array($rk, $columns)) {
                $columnName = $tableName . '.' . $rk;
                if (is_null($rv)) {
                    $query->nullColumn($columnName);
                } else if (mb_substr($rv, 0, 1) == '%' && mb_substr($rv, -1, 1) == '%') {
                    $where[] = [$columnName, 'LIKE', $rv];
                } else {
                    $where[] = [$columnName, '=', $rv];
                }
            }
        }

        return $where;
    }

    /**
     * 限制查询字段为空的
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNullColumn($query, $column)
    {
        return $query->whereNull($column);
    }
}
