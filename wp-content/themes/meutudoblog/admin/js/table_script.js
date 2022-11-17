jQuery(function($) {
    
    // Get and save infos
    function getInfosAcf () {
        tables = [];
        inputs = $('.acf-postbox input[type="checkbox"], .acf-postbox input[type="text"], .acf-postbox input[type="number"], .acf-postbox input.table[type="hidden"]');
        inputCheck = null;
        i = 1;
        inputs.each(function() {
            inputName = $(this).attr('name');
            inputId = $(this).attr('id');
            inputVal = $(this).val();
            inputNameAcf = inputName.match(/field_[a-z0-9]+/g)[0];

            // ignore clone inputs
            if (/acfclone/i.test(inputId)) return;

            // Check is subtitles
            isSubtitle = false;
            if (inputNameAcf == inputCheck) isSubtitle = true;
            inputCheck = inputNameAcf;
            
            // Save infos by key ids
            if (i == 1) tables['title_show'] = $(this).is(":checked");
            else if (i == 2) tables['title_label'] = inputVal;
            else if (i == 3) tables['subtitle_show'] = $(this).is(":checked");
            else if (i == 4 && !isSubtitle) tables['subtitle_label'] = [inputVal];
            else if (isSubtitle) tables['subtitle_label'].push(inputVal);
            else if (i == 5) tables['caption'] = inputVal;
            else if (i == 6) tables['table'] = decodeURIComponent(inputVal);
            
            if (!isSubtitle) i++;
        });

        return tables;
    }

    // Create table structure
    function createTable(table) {
        cols = 1;
        html = '<table style="width:100%;text-align:center;background:#f2f2f2;padding:10px;border-radius:10px;">';
        htmlContent = '';

        // caption
        if (table.caption != null) {
            //html += '<caption style="caption-side:bottom;font-size:.8rem;font-style:italic;">' + table.caption + '</caption>';
        }

        // body > contents
        if (table.table != null) {
            tableContent = jQuery.parseJSON(table.table);
            $.each(tableContent, function(i, item) {
                if (i == 'b') {
                    $.each(item, function(i, tr) {
                        cols = tr.length;
                        htmlContent += '<tr>';
                        $.each(tr, function(i, td) {
                            htmlContent += '<td>' + td.c + '</td>';
                        });
                        htmlContent += '</tr>';
                    });
                }
            });
        }

        // header > title
        if (table.title_show && table.title_label != null) {
            html += '<thead><tr><th colspan="' + cols + '">' + table.title_label + '</th></tr></thead>';
        }

        html += '<tbody>';

        // body > subtitle
        if (table.subtitle_show && table.subtitle_label.length !== 0) {
            html += '<tr>';
            table.subtitle_label.forEach(function(item) {
                html += '<td colspan="' + (cols / table.subtitle_label.length).toFixed() + '"><u>' + item + '</u></td>';
            });
            html += '</tr>';
        }

        html += htmlContent;
        html += '</tbody></table>';

        // write table
        messageHtml = $('.acf-postbox .acf-field-message .acf-input').html();
        $('.acf-postbox .acf-field-message .acf-input').html(
            '<b>Pré-visualização da tabela:</b><br><small>(salve o post para atualizar a pré-visualização da tabela)</small><br><br>' + 
            html + '<br><hr><br>' + messageHtml
        );
    }

    // init
    table = getInfosAcf();
    createTable(table);
});