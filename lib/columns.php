<?php

class Columns
{


    public static function addButtonm(rex_extension_point $ep, array $btn)
    {
        $items = (array) $ep->getSubject();
        $items[] = $btn;
        $ep->setSubject($items);
    }


    public static function addButtons(rex_extension_point $ep)
    {
        foreach (['copy', 'cut'] as $type) {
            static::addButtonm($ep, [
                'hidden_label' => 'HIDDEN LABEL',
                // 'url' => rex_url::backendController([
                //     'page' => 'content/edit',
                //     'article_id' => $ep->getParam('article_id'),
                //     'bloecks' => 'cutncopy',
                //     'module_id' => $ep->getParam('module_id'),
                //     'slice_id' => $ep->getParam('slice_id'),
                //     'clang' => $ep->getParam('clang'),
                //     'ctype' => $ep->getParam('ctype'),
                //     'revision' => 1,
                //     'cuc_action' => $type,
                // ]),
                'attributes' => [
                    'class' => ['btn-' . $type],
                    'title' => 'TITLE',
                    'data-bloecks-cutncopy-iscopied' => 0 && ('edit' === $type) ? 'true' : 'false',
                    'data-pjax-no-history' => 'true',
                ],
                'icon' => '',
            ]);
        }
    }

    public static function show($ep)
    {
        $subject = $ep->getSubject();

        $attributes = [];

        if (rex::isBackend()) {
            if (!preg_match('/<form/', $subject)) {
                // $subject = '<li class="rex-slice rex-slice-bloecks-item rex-slice-output"' . (!empty($attributes) ? ' ' . join(' ', $attributes) : '') . '><ul>' . $subject . '</ul></li>';
                // dump($ep);
                // dump($subject);

                $sql = rex_sql::factory();
                $res = $sql->setQuery('select slice_size from rex_article_slice where id = :id', ['id' => $ep->getParam('slice_id')]);

                $width = $res->getValue('slice_size');

                if ($width == '') {
                    $width = 6;
                }

                $css_width = 100 * ($width / 6) . '%';
                $css_width = str_replace(",", ".", $css_width);


                // sortablejs
                $subject = '<li class="dragdrop" style="width:' . $css_width . '" data-width="' . $width . '" data-id="' . $ep->getParam('slice_id') . '"><ul>' . $subject . '</ul></li>';


                // gridstack
                // $subject = '<li class="grid-stack-item" data-id="' . $ep->getParam('slice_id') . '"><ul><div class="grid-stack-item-content">' . $subject . '</div></ul></li>';

                //     $subject .= '<div class="grid-stack">
                //     <div class="grid-stack-item">
                //       <div class="grid-stack-item-content">Item 1</div>
                //     </div>
                //     <div class="grid-stack-item" gs-w="2">
                //       <div class="grid-stack-item-content">Item 2 wider</div>
                //     </div>
                //   </div>';


                // dump($subject);
            }
        } else {

            // $subject = 'HELLOOOO';
            // echo 'HHHHHHHHHHHHHHHHHHHHH';
        }

        return $subject;
    }

    public static function frontend($ep)
    {
        $subject = $ep->getSubject();
        $find = '{{bloecks_columns_css}}';

        $size = static::getSize($ep->getParam('slice_id'));

        if ($size == '') {
            $size = 6;
        }

        // dump($size);
        // $subject = $ep->getSubject();

        // $subject = '<div class="col-sm-3"><hr><hr><hr>' . $subject . '</div>';

        // return $subject;

        if (($p = strpos($subject, $find)) !== false) {
            $subject = substr($subject, 0, $p) . substr($subject, $p + strlen($find));
        } else {
            $subject =  "\n" .
                "echo '<div class=\"" . 'col-sm-' . $size*2 . "\">'; // bloecks_columns" .
                "\n\n" .
                $subject .
                "\n" .
                "echo '</div>'; // bloecks_columns wrapper" .
                "\n";
        }
        // dump($subject);
        return $subject;
    }

    private static function getSize($sliceID)
    {
        $sql = rex_sql::factory();
        $res = $sql->setQuery('select slice_size from rex_article_slice where id = :id', ['id' => $sliceID]);

        $width = $res->getValue('slice_size');

        return $width;
    }
}