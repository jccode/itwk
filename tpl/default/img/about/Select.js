$.fn.divSelect = function(data, f) {
    var inOb = $(this);
    var id = 'divSelect' + $(this).attr("id");
    var width = $(this).width();
    var aStyle = {
        'float': 'left',
        'padding-top': '3px',
        'padding-left': '3px',
        'overflow': 'hidden',
        'display': 'inline',
        'height': '17px'
    };
    var bStyle = {
        'border': '1px solid #FFF',
        'background-color': '#FFF',
        'position': 'absolute',
        'overflow-y': 'auto',
        'overflow-x': 'hidden',
        'z-index': 20,
        'margin': 0,
        'padding': 0,
        'border': 'solid 1px #CCC'
    };
    var cStyle = {
        'padding-top': '3px',
        'padding-left': '0px',
        'overflow': 'hidden',
        'float': 'left',
        'width': '17px',
        'height': '17px',
        'text-align': 'center',
        'display': 'inline',
        'background-color': '#FFF'
    };
    var liStyle = {
        'line-height': '20px',
        'list-style': 'none',
        'margin': 0,
        'padding': 0,
        'cursor':'pointer'
    };
    $(this).hide();
    $(this).parent().append('<div id="a_' + id + '">&nbsp;</div><div id="c_' + id + '"><img src="/skin/1/images/icon_select.png" style=""vertical-align:middle;""></div>' + '<div id="b_' + id + '"></div>');
    var top = $(this).offset().top;
    left = $(this).offset().left;
    $('#a_' + id).css(aStyle).css({
        'width': width - 22
    }).html('').html($(this).val());
    $('#c_' + id).css(cStyle).click(function(eb) {
        $('#b_' + id).show();
        try {
            eb.stopPropagation();
        }
        catch (e) {
            event.cancelBubble = true;
        }
    });
    $('#b_' + id).css(bStyle).css({
        'width': width - 2,
        'top': $('#a_' + id).offset().top + 20,
        'left': $('#a_' + id).offset().left
    }).hide();
    $('<ul style="margin:0;padding:3px"></ul>').appendTo($('#b_' + id));
    if (data.length > 9) {
        $('#b_' + id).css({
            'height': '206px'
        });
    }
    for (i = 0; i < data.length; i++) {
        $("<li/>").html(data[i][1]).attr('v', data[i][0]).css(liStyle).click(function(eb) {
            $(inOb).val($(this).attr('v'));
            $('#a_' + id).html('').html($(this).html());
            $('#b_' + id).hide()
            if (f)
                f($(this).attr('v'), $(this).html());
            try {
                eb.stopPropagation();
            }
            catch (e) {
                event.cancelBubble = true;
            }
        }).hover(function() {
            $('#b_' + id).find('li[class=floatred]').removeClass('floatred');
            $(this).addClass('floatred');
        }, function() {
            $(this).removeClass('floatred');
        }).appendTo($('#b_' + id + ' ul'));
    }
    var liobj = $('#b_' + id).find('li[v=' + inOb.val() + ']');
    if (liobj.html()) {
        liobj.addClass('floatred');
        $('#a_' + id).html(liobj.html());
    }
    else {
        inOb.val('');
    }
    $(document).click(function() {
        $("#b_" + id).hide();
    });
};

