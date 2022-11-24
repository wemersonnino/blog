<?php

/**
 * Widget: Tabela
 * Shortcode: [mt_tabela id=""]
 */
add_shortcode('mt_tabela', function ($atts) {
    $id = $atts['id'];
    if (empty($id) || get_post_status($id) != 'publish') return null;

    // get fields acf
    $titleShow = get_field('title_show', $id);
    $titleLabel = get_field('title_label', $id);
    $subtitleShow = get_field('subtitle_show', $id);
    $subtitleLabels = get_field('subtitle_labels', $id);
    $caption = get_field('caption', $id);
    $table = get_field('table', $id);
    $htmlCaption = $htmlHead = $htmlSubtitle = $htmlContent = null;

    // infos
    $cols = $subtitlesCount = 1;
    if (!empty($table)) foreach ($table['body'] as $key => $tr) $cols = count($tr);

    // caption
    if (!empty($caption)) {
        $htmlCaption .= "<caption>{$caption}</caption>";
    }

    // header > title
    if ($titleShow && !empty($titleLabel)) $htmlHead .= "<thead><tr><th colspan=\"{$cols}\">{$titleLabel}</th></tr></thead>";

    // body > subtitle
    if ($subtitleShow && !empty($subtitleLabels)) {
        $subtitles = [];
        $htmlSubtitle .= "<tr class='subtitle'>";
        foreach ($subtitleLabels as $v) {
            $subtitles[] = $v['subtitle_label'];
            $subtitlesCount = count($subtitleLabels);
            $htmlSubtitle.= "<td scope='col' colspan='" . round($cols / $subtitlesCount) . "'><span>{$v['subtitle_label']}</span></td>";
        }
        $htmlSubtitle .= "</tr>";
    }

    // body > contents
    if (!empty($table)) {
        foreach ($table['body'] as $key => $tr) {
            $classContent = count($table['body']) == 1 ? 'content-only' : null;
            $htmlContent .= "<tr class=\"content content-{$key} {$classContent}\">";
            foreach ($tr as $k => $td) {
                $dataRow = $key > 0 && $k == 0 ? 'scope="row"' : null;
                $dataLabel = $cols == $subtitlesCount ? "data-label='{$subtitles[$k]}'" : null;
                $spanClass = strlen($td['c']) > 100 ? 'text-long' : null;
                $htmlContent .= "<td {$dataRow} {$dataLabel}><span class='{$spanClass}'>{$td['c']}</span></td>";
            }
            $htmlContent .= "</tr>";
        }
    }

    // return html content
    $tableClass = !$titleShow || empty($titleLabel) ? 'no-head' : null;
    $tableClass .= $cols == $subtitlesCount ? ' responsive' : null;
    $html = "<table class=\"{$tableClass}\">" . $htmlCaption . $htmlHead . "<tbody>" . $htmlSubtitle . $htmlContent . "</tbody></table>";
    return "<div class=\"mt-table\">{$html}</div>";
});