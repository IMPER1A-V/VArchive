$(document).ready(function(){
    var offset = 0;
    var offset_search = 0;
    var offset_search_date = 0;
    var user_id = 0;
    var loading = false;
    var get_attachments = false;
	var searchResultsFound = false;
	var searchText;
	var search_date;
	
    $('.user_item').click(function(){
		 $('.user_item').css('background-color', '');
    $(this).css('background-color', '#397dcc');
        user_id = $(this).data('id');
        offset = 0;
        $(".content").empty();
        get_messages();
		
		 if ($('.vk_content .header-content_left').length) {
        $('.vk_content .header-content_left p').text('TEXT');
    } else {
		 $('.vk_content').prepend('<div class="header-content"><div class="header-content_left"><p></p></div><div class="header-content_right">'+
		 '<a class="action_menu" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg></a><svg class="search-button" xmlns="http://www.w3.org/2000/svg" height="33" viewBox="0 0 24 24" width="33"><path d="m20.7 19.3-3.1-3.1c.9-1.2 1.4-2.6 1.4-4.2 0-3.9-3.1-7-7-7s-7 3.1-7 7 3.1 7 7 7c1.6 0 3-.5 4.2-1.4l3.1 3.1c.2.2.5.3.7.3s.5-.1.7-.3c.4-.4.4-1 0-1.4zm-13.7-7.3c0-2.8 2.2-5 5-5s5 2.2 5 5-2.2 5-5 5-5-2.2-5-5z"/></svg>'+
		 '</а>'+'<ul class="dropdown-menu dropdown-menu-end profile_menu"><li><a data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="profile_row"><div class="menu_item_icon"></div><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#818C99"><path d="M14.95 3.8a2.72 2.72 0 00-3.86 0L5.56 9.35a4.5 4.5 0 000 6.34 4.46 4.46 0 006.32 0l2.88-2.86a.75.75 0 011.06 1.06l-2.88 2.86a5.96 5.96 0 01-8.44 0 6 6 0 010-8.46l5.53-5.55a4.22 4.22 0 015.98 0 4.24 4.24 0 010 6l-5.53 5.54a2.49 2.49 0 11-3.52-3.52l3.1-3.09a.75.75 0 011.06 1.07l-3.1 3.08a1 1 0 000 1.4.99.99 0 001.4 0l5.53-5.55a2.74 2.74 0 000-3.87z"/></svg><span>Показать вложения</span></a></li></ul> <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="staticBackdropLabel">Фотографии</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button></div><div class="modal-body"></div></div></div></div>'+
		 '<div class="modal_attachments"><span class="modal_attachments_close">&times;</span><div class="modal_attachments_wrapper"><img class="modal_attachments_content" id="modal-image"><img id="arrow-left" src="img/icons/arrow-left.svg"><img id="arrow-right" src="img/icons/arrow-right.svg"></div></div>'+
		 '<div class="date_fixed"></div></div>');
	

	}

$('.header-content_left p').text($(this).find('.item_column_name span').text());

  if ($('.search-results').length) {
      	$('.content').show();
$('.search-results').remove();
	 $('.search-container').hide();
    $('.header-content_left').show();
    $('.header-content_right').css('width', '');
	    $('.ui_actions_menu').css('display', 'block');
		    $('.search-button').css('margin', '');
	
  
  $('input[type="text"]').val(''); 
			
    }
	if ($('.search-container').length) { 
$('.search-container').remove();
 $('.header-content_left, .search-button').show();
    $('.header-content_right').css('width', '');
	    $('.ui_actions_menu').css('display', 'block');
		    $('.search-button').css('margin', '');
}
    });
    
    function get_messages() {
        if (loading) {
            return;
        }
        loading = true;
        $.ajax({
            url: 'get_messages.php',
            type: 'GET',
           data: { action: 'get_messages', offset: offset, id: user_id },
            dataType: 'json',
            success: function(data) {
				
                var old_height = $(".content")[0].scrollHeight;
              
                $.each(data, function(index, item) {
					 var profile_img = item.name == 'Вы' ? item.admin_profile_img : item.user_profile_img;
                     var column = $(".column[data-date='" + item.date + "']");
    if (column.length === 0) {
        $(".content").prepend("<div class='column' data-date='" + item.date + "'></div>");
        $(".content").prepend("<div class='date'>" + item.date + "</div>");
        column = $(".column[data-date='" + item.date + "']");
    }

                    var time_parts = item.time.split(":");
                    time_parts = time_parts.slice(0, -1);
                    var time_without_seconds = time_parts.join(":");
                    var content_item = $("<div class='content_item'></div>");
					let contentTag = (item.content.includes("https://") || item.content.includes("http://")) ? "<a href='" + item.content + "'>" + item.content + "</a>" : "<p>" + item.content + "</p>";
content_item.append("<div class='col_l'><img src='" + profile_img + "'></div><div class='col_r'><div class='col_t'> <a href='#'>" + item.name + " </a><span>" + time_without_seconds + "</span></div><div class='col_b'>" + contentTag + "</div></div>");
                    if (item.images) {
                        content_item.find('.col_b').append("<img src='" + item.images + "' width='100%'>");
                    }
					if (item.stickers) {
    content_item.find('.col_b').append("<img src='" + item.stickers + "' width='55%'>");
}
if (item.audio) {
    content_item.find('.col_b').append("<audio controls><source src='" + item.audio + "' type='audio/ogg'></audio>");
}


                    $(".column[data-date='" + item.date + "']").prepend(content_item);
                });
                offset += data.length;
                var new_height = $(".content")[0].scrollHeight;
                var added_height = new_height - old_height;
                $(".content").scrollTop(added_height);
                loading = false;
            }
        });
    }
	//вывод материалов беседы пользователя
function get_attachments_user() {
    console.log('get_attachments_user called');

    if (get_attachments) {
        return;
    }
    get_attachments = true;
    $.ajax({
        url: 'get_messages.php',
        type: 'GET',
        data: { action: 'attachments_user', id: user_id },
        dataType: 'json',
        success: function(data) {
            console.log(data);
            var row = $("<div class='row'></div>");
            $(".modal-body").prepend(row);
            $.each(data, function(index, item) {
                var content_item = $("<div class='attachments_col padding-modal' ></div>");
                row.append(content_item);

                if (item.images) {
                    content_item.append("<img src='" + item.images + "' style='height:158px;object-fit: cover;width: 100%;'>");
                }
            });
            // Вызываем функцию setColClass после добавления элементов
            setColClass();
          
        }
    });
    console.log('get_attachments_user finished');
}
function setColClass() {
    // Получаем все элементы с классом attachments_col на странице
    var cols = document.querySelectorAll('.attachments_col');
    // Перебираем все элементы
    for (var i = 0; i < cols.length; i++) {
        // Получаем изображение внутри элемента
        var image = cols[i].querySelector('img');
        // Получаем ширину и высоту изображения
        var width = image.naturalWidth;
        var height = image.naturalHeight;
        // Удаляем все классы col- у элемента
        for (var j = 1; j <= 12; j++) {
            cols[i].classList.remove('col-' + j);
        }
        // Назначаем соответствующий класс col- в зависимости от ширины и высоты изображения
        if (width > height) {
            cols[i].classList.add('col-4');
        }
		else {
            cols[i].classList.add('col-2');
        }
    }
}








    $(".content").scroll(function() {
	

	var maxScrollTop = $('.content').prop('scrollHeight') - $('.content').innerHeight();
console.log(maxScrollTop);
if ($(this).scrollTop() < maxScrollTop - 300) {
    var navigation = $(".im-navigation");
    if (navigation.length === 0) {
        $('.content').append('<div class="im-navigation"><div class="im-navigation__button"><svg xmlns="http://www.w3.org/2000/svg" height="7" viewBox="0 0 14 7" width="14"><path d="M1.64.232c-.424-.354-1.055-.296-1.408.128s-.296 1.055.128 1.408l6 5c.371.309.91.309 1.28 0l6-5c.424-.354.482-.984.128-1.408s-.984-.482-1.408-.128l-5.36 4.467z" fill="#656565"/></svg></div></div>');
    }
    $('.im-navigation__button').fadeIn();
} else {
    $('.im-navigation__button').fadeOut();
}

        if ($(this).scrollTop() == 0) {
            get_messages();
        }
		
    var scrollTop = $(this).scrollTop();
    var contentHeight = $(this).height();

    $(".column").each(function() {
        var firstItem = $(this).find(".content_item:first");
        var lastItem = $(this).find(".content_item:last");
        var firstItemTop = firstItem.position().top;
        var lastItemTop = lastItem.position().top;
        var lastItemHeight = lastItem.height();

        if (firstItemTop < scrollTop + contentHeight && lastItemTop + lastItemHeight > scrollTop) {
            $(".date_fixed").text($(this).data("date"));
            return false;
        }
    });
    });
$(document).on('click', '.im-navigation__button', function() { //используется при действиях с динамическим добавлением элементов
  $(".content").animate({ scrollTop: $(".content")[0].scrollHeight }, "slow");
  });

//
////////////////////////////////////////////////////
$(document).on('click', '.search-button', function() {
		 $('.header-content_right').append('<div class="search-container"><input type="text" placeholder="Поиск по истории сообщений"><span id="calendar"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20"><path fill="currentColor" fill-rule="evenodd" d="M6.25 1a.75.75 0 0 1 .75.75v.26L8.32 2H13v-.25a.75.75 0 0 1 1.5 0v.36c.43.06.82.17 1.18.35a4.25 4.25 0 0 1 1.86 1.86c.25.5.36 1.04.41 1.67.05.61.05 1.38.05 2.33v3.36c0 .96 0 1.72-.05 2.33a4.36 4.36 0 0 1-.41 1.67 4.25 4.25 0 0 1-1.86 1.86c-.5.25-1.04.36-1.67.41-.61.05-1.37.05-2.33.05H8.32c-.96 0-1.72 0-2.33-.05a4.36 4.36 0 0 1-1.67-.41 4.25 4.25 0 0 1-1.86-1.86A4.36 4.36 0 0 1 2.05 14C2 13.4 2 12.64 2 11.68V8.32c0-.96 0-1.72.05-2.33.05-.63.16-1.17.41-1.67a4.25 4.25 0 0 1 1.86-1.86c.36-.18.75-.29 1.18-.35v-.36A.75.75 0 0 1 6.25 1ZM5.5 3.63c-.2.04-.36.1-.5.17A2.75 2.75 0 0 0 3.8 5c-.13.25-.21.58-.25 1.11-.03.26-.04.54-.04.88h12.98c0-.34-.01-.62-.04-.88a2.91 2.91 0 0 0-.25-1.1A2.75 2.75 0 0 0 15 3.8c-.14-.07-.3-.13-.5-.17v.12a.75.75 0 0 1-1.5 0v-.24l-1.35-.01H7v.25a.75.75 0 0 1-1.5 0v-.12Zm11 4.86h-13v3.16c0 1 0 1.7.05 2.24.04.54.12.86.25 1.1A2.75 2.75 0 0 0 5 16.2c.25.13.58.21 1.11.25.55.05 1.25.05 2.24.05h3.3c1 0 1.7 0 2.24-.05.53-.04.86-.12 1.1-.25A2.75 2.75 0 0 0 16.2 15c.13-.25.21-.58.25-1.11.05-.55.05-1.25.05-2.24V8.49ZM15 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"></path></svg></span><button class="search">Поиск</button><button class="close-button">Отмена</button></div></div>');
    $('.header-content_left, .search-button, .action_menu').hide();
    $('.header-content_right').css('width', '100%');
    $('.search-container').css('width', 'inherit');
    $('.ui_actions_menu').css('display', 'none');
$('#calendar').datepicker({
    language: 'ru',
    orientation: 'bottom right',
    format: 'yyyy/mm/dd',
    autoclose: true
}).on('changeDate', function(e){
    // Получение выбранной даты в локальном формате
    var localDate = e.date.toLocaleDateString('ru-RU', {year: 'numeric', month: '2-digit', day: '2-digit'});
  var year = e.date.getFullYear();
var month = ('0' + (e.date.getMonth() + 1)).slice(-2);
var day = ('0' + e.date.getDate()).slice(-2);
var formattedDate = year + '-' + month + '-' + day;
if ($('.search-results').length) {
		
        $('.search-results').remove();
    }
	search_date = formattedDate;
	   console.log(search_date);
	   if (search_date) {
   console.log('не пусто');
    $('.content').hide();
    $('.vk_content .content').before('<div class="search-results"></div>');
		 $('.date_fixed').hide();
   get_search_date();
}

});



  });
  
  $(document).on('click', '.close-button', function() {
	$('.content, .search-button, .action_menu').show();
$('.search-results').remove();

	 $('.search-container').remove();
    $('.header-content_left').show();
    $('.header-content_right').css('width', '');
	    $('.ui_actions_menu').css('display', 'block');
		    $('.search-button').css('margin', '');
	
  $('input[type="text"]').val(''); 
  		if ($('.date_fixed').is(':hidden')) {
   $('.date_fixed').show();
}
});
////////////////////////////////////////////////////SEARCH РЕАЛІЗАЦИЯ


	$(document).on('click', '.search', function() {
    if ($('.search-results').length) {
		
        $('.search-results').remove();
    }
			if ($('.date_fixed').is(':hidden')) {
   $('.date_fixed').show();
}
 searchResultsFound = false;
	offset_search = 0;
	  var offset_search_date = 0;
		 searchText = $('input[type="text"]').val(); 
		 
		 if ($('input[type="text"]').val() === '') {
   console.log('пусто');
   

} else {
    $('.content').hide();
    $('.vk_content .content').before('<div class="search-results"></div>');
	get_search();
	 console.log('пусто'+get_search());
}


	

	
	$('.search-results').on('scroll', function() {
        if ($(this).scrollTop() == 0) {
            get_search();
        }

	var maxScrollTop = $('.search-results').prop('scrollHeight') - $('.search-results').innerHeight();
console.log(maxScrollTop);
if ($(this).scrollTop() < maxScrollTop - 300) {
    var navigation = $(".im-navigation");
    if (navigation.length === 0) {
        $('.search-results').append('<div class="im-navigation"><div class="im-navigation__button"><svg xmlns="http://www.w3.org/2000/svg" height="7" viewBox="0 0 14 7" width="14"><path d="M1.64.232c-.424-.354-1.055-.296-1.408.128s-.296 1.055.128 1.408l6 5c.371.309.91.309 1.28 0l6-5c.424-.354.482-.984.128-1.408s-.984-.482-1.408-.128l-5.36 4.467z" fill="#656565"/></svg></div></div>');
    }
    $('.im-navigation__button').fadeIn();
} else {
    $('.im-navigation__button').fadeOut();
}
    var scrollTop = $(this).scrollTop();
    var contentHeight = $(this).height();

    $(".column").each(function() {

        var firstItem = $(this).find(".search_item:first");
        var lastItem = $(this).find(".search_item:last");
        var firstItemTop = firstItem.position().top;
        var lastItemTop = lastItem.position().top;
        var lastItemHeight = lastItem.height();


        if (firstItemTop < scrollTop + contentHeight && lastItemTop + lastItemHeight > scrollTop) {
          
            $(".date_fixed").text($(this).data("date"));
         
            return false;
        }
    });
});
	$(document).on('click', '.im-navigation__button', function() { 
  $(".search-results").animate({ scrollTop: $(".search-results")[0].scrollHeight }, "slow");
  });
	
});

//Поиск по CONTENT
function get_search() {
	if (loading) {
            return;
        }
        loading = true;
        $.ajax({
            url: 'get_messages.php',
            type: 'GET',
            data: { offset_s: offset_search, search: searchText , id: user_id,  action: 'get_search' },
            dataType: 'json',
            success: function(data) {
				  var old_height = $(".search-results")[0].scrollHeight;
              if (data.length > 0) {
              searchResultsFound = true;
                $.each(data, function(index, item) {
                
					  var column = $(".search-results .column[data-date='" + item.date + "']");
    if (column.length === 0) {
        $(".search-results").prepend("<div class='column' data-date='" + item.date + "'></div>");
        $(".search-results").prepend("<div class='date'>" + item.date + "</div>");
        column = $(".search-results .column[data-date='" + item.date + "']");
    }

                    var time_parts = item.time.split(":");
                    time_parts = time_parts.slice(0, -1);
                    var time_without_seconds = time_parts.join(":");
                    var search_item = $("<div class='search_item'></div>");
                    search_item.append("<div class='col_l'><img src='" + item.profile_img + "'></div><div class='col_r'><div class='col_t'> <a href='#'>" + item.name + " </a><span>" + time_without_seconds + "</span></div><div class='col_b'><p>" + item.content + "</p></div></div>");
                    if (item.images) {
                        search_item.find('.col_b').append("<img src='" + item.images + "' width='100%'>");
                    }
                $(".search-results .column[data-date='" + item.date + "']").prepend(search_item);
                });
               
				offset_search += data.length;
                loading = false;
				 var new_height = $(".search-results")[0].scrollHeight;
                var added_height = new_height - old_height;
                $(".search-results").scrollTop(added_height);
				}
				else if (!searchResultsFound) {
   $('.date_fixed').hide();
	 $('.search-results').append("<div class='no-results'><svg style='color:#656565;margin-bottom: 23px;' width='56' height='56' viewBox='0 0 56 56' fill='none' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' clip-rule='evenodd' d='M22.03 10c-8.48 0-14.97 5.92-14.97 12.8 0 2.47.82 4.79 2.25 6.74a1.5 1.5 0 0 1 .3.9c0 1.63-.43 3.22-.96 4.67a41.9 41.9 0 0 1-1.17 2.8c3.31-.33 5.5-1.4 6.8-2.96a1.5 1.5 0 0 1 1.69-.43 17.06 17.06 0 0 0 6.06 1.1C30.5 35.61 37 29.68 37 22.8 37 15.93 30.5 10 22.03 10zM4.06 22.8C4.06 13.9 12.3 7 22.03 7 31.75 7 40 13.88 40 22.8c0 8.93-8.25 15.81-17.97 15.81-2.17 0-4.25-.33-6.17-.95-2.26 2.14-5.55 3.18-9.6 3.34a2.2 2.2 0 0 1-2.07-3.08l.42-.95c.43-.96.86-1.9 1.22-2.9.41-1.11.69-2.18.76-3.18a14.28 14.28 0 0 1-2.53-8.08z' fill='#828282'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M43.01 18.77a1.5 1.5 0 0 0 .38 2.09c3.44 2.38 5.55 5.98 5.55 9.95 0 2.47-.81 4.78-2.25 6.73a1.5 1.5 0 0 0-.3.9c0 1.63.43 3.22.96 4.67.35.96.77 1.92 1.17 2.8-3.31-.33-5.5-1.4-6.8-2.96a1.5 1.5 0 0 0-1.69-.43 17.06 17.06 0 0 1-6.06 1.1c-2.98 0-5.75-.76-8.08-2.03a1.5 1.5 0 0 0-1.44 2.63 20.19 20.19 0 0 0 15.7 1.44c2.25 2.14 5.54 3.18 9.59 3.34a2.2 2.2 0 0 0 2.07-3.08l-.42-.95c-.44-.96-.86-1.9-1.22-2.9a11.65 11.65 0 0 1-.76-3.18 14.28 14.28 0 0 0 2.53-8.08c0-5.1-2.72-9.56-6.84-12.42a1.5 1.5 0 0 0-2.09.38z' fill='#828282'></path></svg><p style='color:#b2b2b2;'>Не найдено сообщений по такому запросу.</p></div>");

 }
			},complete: function() {
        loading = false;
    }
        });

}
//ПОИСК ПО ДАТЕ

function get_search_date() {
	if (loading) {
            return;
        }
        loading = true;
        $.ajax({
            url: 'get_messages.php',
            type: 'GET',
            data: { offset_s: offset_search_date, search: search_date , id: user_id, action: 'get_search_date',},
            dataType: 'json',
            success: function(data) {
				  var old_height = $(".search-results")[0].scrollHeight;
              if (data.length > 0) {
              
                $.each(data, function(index, item) {
                
					  var column = $(".search-results .column[data-date='" + item.date + "']");
    if (column.length === 0) {
        $(".search-results").prepend("<div class='column' data-date='" + item.date + "'></div>");
        $(".search-results").prepend("<div class='date'>" + item.date + "</div>");
        column = $(".search-results .column[data-date='" + item.date + "']");
    }

                    var time_parts = item.time.split(":");
                    time_parts = time_parts.slice(0, -1);
                    var time_without_seconds = time_parts.join(":");
                    var search_item = $("<div class='search_item'></div>");
                    search_item.append("<div class='col_l'><img src='" + item.profile_img + "'></div><div class='col_r'><div class='col_t'> <a href='#'>" + item.name + " </a><span>" + time_without_seconds + "</span></div><div class='col_b'><p>" + item.content + "</p></div></div>");
                    if (item.images) {
                        search_item.find('.col_b').append("<img src='" + item.images + "' width='100%'>");
                    }
                $(".search-results .column[data-date='" + item.date + "']").prepend(search_item);
                });
               
				offset_search_date += data.length;
                loading = false;
				 var new_height = $(".search-results")[0].scrollHeight;
                var added_height = new_height - old_height;
                $(".search-results").scrollTop(added_height);
				}
				else {
 
	 $('.search-results').append("<div class='no-results'><svg style='color:#656565;margin-bottom: 23px;' width='56' height='56' viewBox='0 0 56 56' fill='none' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' clip-rule='evenodd' d='M22.03 10c-8.48 0-14.97 5.92-14.97 12.8 0 2.47.82 4.79 2.25 6.74a1.5 1.5 0 0 1 .3.9c0 1.63-.43 3.22-.96 4.67a41.9 41.9 0 0 1-1.17 2.8c3.31-.33 5.5-1.4 6.8-2.96a1.5 1.5 0 0 1 1.69-.43 17.06 17.06 0 0 0 6.06 1.1C30.5 35.61 37 29.68 37 22.8 37 15.93 30.5 10 22.03 10zM4.06 22.8C4.06 13.9 12.3 7 22.03 7 31.75 7 40 13.88 40 22.8c0 8.93-8.25 15.81-17.97 15.81-2.17 0-4.25-.33-6.17-.95-2.26 2.14-5.55 3.18-9.6 3.34a2.2 2.2 0 0 1-2.07-3.08l.42-.95c.43-.96.86-1.9 1.22-2.9.41-1.11.69-2.18.76-3.18a14.28 14.28 0 0 1-2.53-8.08z' fill='#828282'></path><path fill-rule='evenodd' clip-rule='evenodd' d='M43.01 18.77a1.5 1.5 0 0 0 .38 2.09c3.44 2.38 5.55 5.98 5.55 9.95 0 2.47-.81 4.78-2.25 6.73a1.5 1.5 0 0 0-.3.9c0 1.63.43 3.22.96 4.67.35.96.77 1.92 1.17 2.8-3.31-.33-5.5-1.4-6.8-2.96a1.5 1.5 0 0 0-1.69-.43 17.06 17.06 0 0 1-6.06 1.1c-2.98 0-5.75-.76-8.08-2.03a1.5 1.5 0 0 0-1.44 2.63 20.19 20.19 0 0 0 15.7 1.44c2.25 2.14 5.54 3.18 9.59 3.34a2.2 2.2 0 0 0 2.07-3.08l-.42-.95c-.44-.96-.86-1.9-1.22-2.9a11.65 11.65 0 0 1-.76-3.18 14.28 14.28 0 0 0 2.53-8.08c0-5.1-2.72-9.56-6.84-12.42a1.5 1.5 0 0 0-2.09.38z' fill='#828282'></path></svg><p style='color:#b2b2b2;'>Не найдено сообщений по такому запросу.</p></div>");

 }
			},complete: function() {
        loading = false;
    }
        });

}


//////////////////////////////////////////////////
	
   $('#file-names').on('click', function() {
        $('#file-upload').click();
    });
    $('#file-upload').on('change', function() {
        $('.file-names-placeholder').remove();
        var filenames = '';
        for (var i = 0; i < this.files.length; i++) {
            filenames += '<li class="file-names_ul">' + this.files[i].name + '</li>';
        }
        $('#file-names').html(filenames);
    });
  //////////
$(window).on('shown.bs.modal', function() { 
get_attachments_user();
});


///modal image attachments_col////////////////////////
  let attachments;

   $(document).on('click', '.attachments_col', function() {
	   attachments = $('.attachments_col');
    let imgSrc = $(this).find('img').attr('src');
    $('.modal_attachments').css({
        'display': 'flex',
        'align-items': 'center',
        'justify-content': 'center'
    });
    const width = $(window).width() * 0.5;
    const height = $(window).height() * 0.7;
    $('.modal_attachments_content').css({
        width: width + 'px',
        height: height + 'px'
    });
    $('body').css('overflow', 'hidden');
    $('#modal-image').attr('src', imgSrc);
    $('.modal_attachments').show();
    $('.modal, .modal-backdrop').hide();

});
$(document).on('click', '.modal_attachments_close, .modal_attachments', function() {
    $('.modal_attachments').hide();
    $('body').css('overflow', 'auto');
    $('.modal, .modal-backdrop').show();
	    $('#arrow-left').hide();
    $('#arrow-right').hide();
});

let currentIndex = 0;

function showAttachment(index) {
    const imgSrc = attachments.eq(index).find('img').attr('src');
    $('#modal-image').attr('src', imgSrc);
}

$(document).on('click', '#arrow-left', function(event) {
    event.stopPropagation();
    currentIndex = (currentIndex === 0) ? attachments.length - 1 : currentIndex - 1;
    showAttachment(currentIndex);
});

$(document).on('click', '#arrow-right', function(event) {
    event.stopPropagation();
    currentIndex = (currentIndex === attachments.length - 1) ? 0 : currentIndex + 1;
    showAttachment(currentIndex);
});

$(document).on('click', '.modal_attachments_content', function(event) {
    event.stopPropagation();
    const rect = $(this).offset();
    rect.width = $(this).width();
    rect.height = $(this).height();
    const x = event.clientX - rect.left;
    const threshold = rect.width * 0.2;

    if (x < threshold) {
        currentIndex = (currentIndex === 0) ? attachments.length - 1 : currentIndex - 1;
        showAttachment(currentIndex);
    } else if (x > rect.width - threshold) {
        currentIndex = (currentIndex === attachments.length - 1) ? 0 : currentIndex + 1;
        showAttachment(currentIndex);
    }
});
$(document).on('mousemove', '.modal_attachments_content', function(event) {
    const rect = $(this).offset();
    rect.width = $(this).width();
    rect.height = $(this).height();
    const x = event.clientX - rect.left;
    const threshold = rect.width * 0.2;

    if (x < threshold) {
        if ($('#arrow-right').is(':visible')) {
            $('#arrow-right').fadeOut();
        }
        if (!$('#arrow-left').is(':visible')) {
            $('#arrow-left').fadeIn();
        }
    } else if (x > rect.width - threshold) {
        if ($('#arrow-left').is(':visible')) {
            $('#arrow-left').fadeOut();
        }
        if (!$('#arrow-right').is(':visible')) {
            $('#arrow-right').fadeIn();
        }
    } else {
        $('#arrow-left').fadeOut();
        $('#arrow-right').fadeOut();
    }
});



//////////////////////////////////////
});
