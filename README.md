# laravel-utility

## 目录

- [简介](#简介)
- [安装](#安装)
- [Traits](#Traits)
    - [ModelResourceTrait](#ModelResourceTrait)

## 简介

Laravel 框架的实用工具

## 安装

在 Laravel 项目中，执行以下命令：

```bash
composer require dngjnh/laravel-utility
```

## Traits

### ModelResourceTrait

框架模型增删改查的通用 Trait，在模型中引用即可使用里面的方法。

方法说明：

- 查

    - forList

        用于查询列表页数据或某条数据。

        可以链式使用其他 Laravel 框架自带的其他方法。

        接受的参数有：

        - $query：\Illuminate\Database\Eloquent\Builder 实例，不需要手动提供

        - $where：where 条件数组或 id 数字，当为数组时，格式如：

            ```php
            $where = [
                ['field1', '=', 'value1'],
                ['field2', '<>', 'value2'],
            ];
            ```

        - $columns：查询的字段

        - $joins：联合查询，格式如：

            ```php
            $joins = [
                [
                    'innerJoin',
                    'table as t',
                    function ($join) {
                        $join->on('t.filed1', '=', 'model_table_name.field2');
                    }
                ],
                [
                    'leftJoin',
                    'table1 as t1',
                    function ($join) {
                        $join->on('t1.field1', '=', 'model_table_name.field2')
                            ->where('t1.field2', '=', 0);
                    }
                ],
            ];
            ```

        - $withs：关联查询

        - $orders：排序，默认按照 id 倒序

        - $groups：分组

        返回结果：

        接口 URL 中，若无 page 参数，则返回全部数据，否则返回分页数据。

        接口 URL 中，可以提供额外字段筛选条件，参数名只限于存在于所查询的表中才会生效，当参数值首尾不拼接 '%' 时，为精准查询，否则为模糊查询。例如所查询的表中存在 `name` 字段，则 URL 中的 `name=foo` 会查询 `name` 字段值为 'foo' 的结果，`name=%25foo%25` 会查询 `name` 字段值包含 'foo' 的结果。

        举例：

        ```php
        $users = User::forList();
        $admin = User::forList(1)->first();
        ```
