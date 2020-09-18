<?php

$finder = \PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return \PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@Symfony'                    => true,

        /*
         * 以下是取消或修改的 @Symfony 的规则
         */
        // 类型转换后不保留空格
        'cast_spaces'                 => ['space' => 'none'],
        // 字符串连接符左右留空格
        'concat_space'                => ['spacing' => 'one'],
        // 不强制移除没有提供有效信息的 @param 或 @return 标签
        'no_superfluous_phpdoc_tags'  => false,
        // 取消强制的phpdoc tag 替换
        'phpdoc_no_alias_tag'         => false,
        // 取消强制 PHPDoc 摘要应以句号，感叹号或问号结尾
        'phpdoc_summary'              => false,
        // 不强制 Docblock 只应用于结构元素(用于非结构代码的IDE跳转提示)
        'phpdoc_to_comment'           => false,
        // 不强制删除 @var 和 @type 后的变量名(某些情况下影响IDE自动提示)
        'phpdoc_var_without_name'     => false,
        // 不强制单行注释必须以 // 开头
        'single_line_comment_style'   => false,
        // 关闭强制的 Yoda Style
        'yoda_style'                  => false,

        /*
         * 以下是额外添加的规则
         */
        // 二元操作符前后有空格，多行按操作符对齐
        'binary_operator_spaces'      => [
            'default' => 'align_single_space',
        ],
        // 可为null的类型标注在问号和类型间不允许有空间
        'compact_nullable_typehint'   => true,
    ]);
