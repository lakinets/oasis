(function ($) {
  "use strict";

  /*=================================
      JS Index Here
  ==================================*/
  /*
    01. On Load Function
    02. Preloader
    03. Mobile Menu Active
    04. Sticky fix
    05. Scroll To Top
    06. Set Background Image
    07. Hero Slider Active    
    08. Magnific Popup
    09. Filter
    10. Slick 3D Slider
    11. Custom Tab
    12. Smooth Scroll
    13. Custom Cursor
    14. Popup Sidemenu
    15. Search Box Popup
    16. Parallax Effect
    17. Custom Slider Animaiton
    18. Breaking News Slider
    00. Right Click Disable
    00. Inspect Element Disable
  */
  /*=================================
      JS Index End
  ==================================*/

  /*---------- 01. On Load Function ----------*/
  $(window).on('load', function () {
    $('.preloader').fadeOut();
  });

  /*---------- 01.1. Отключаем прыжки при клике на # ----------*/
  document.querySelectorAll('a[href="#"], button[type="button"]').forEach(el => {
    el.addEventListener('click', e => e.preventDefault());
  });

  /*---------- 02. Preloader ----------*/
  if ($('.preloader').length > 0) {
    $('.preloaderCls').each(function () {
      $(this).on('click', function (e) {
        e.preventDefault();
        $('.preloader').css('display', 'none');
      });
    });
  }

  /*---------- 03. Mobile Menu Active ----------*/
  $.fn.vsmobilemenu = function (options) {
    var opt = $.extend({
      menuToggleBtn: '.vs-menu-toggle',
      bodyToggleClass: 'vs-body-visible',
      subMenuClass: 'vs-submenu',
      subMenuParent: 'vs-item-has-children',
      subMenuParentToggle: 'vs-active',
      meanExpandClass: 'vs-mean-expand',
      subMenuToggleClass: 'vs-open',
      toggleSpeed: 400,
    }, options);

    return this.each(function () {
      var menu = $(this);

      function menuToggle() {
        menu.toggleClass(opt.bodyToggleClass);
        var subMenu = '.' + opt.subMenuClass;
        $(subMenu).each(function () {
          if ($(this).hasClass(opt.subMenuToggleClass)) {
            $(this).removeClass(opt.subMenuToggleClass);
            $(this).css('display', 'none');
            $(this).parent().removeClass(opt.subMenuParentToggle);
          }
        });
      }

      menu.find('li').each(function () {
        var submenu = $(this).find('ul');
        submenu.addClass(opt.subMenuClass);
        submenu.css('display', 'none');
        submenu.parent().addClass(opt.subMenuParent);
        submenu.prev('a').addClass(opt.meanExpandClass);
        submenu.next('a').addClass(opt.meanExpandClass);
      });

      function toggleDropDown($element) {
        if ($($element).next('ul').length > 0) {
          $($element).parent().toggleClass(opt.subMenuParentToggle);
          $($element).next('ul').slideToggle(opt.toggleSpeed);
          $($element).next('ul').toggleClass(opt.subMenuToggleClass);
        } else if ($($element).prev('ul').length > 0) {
          $($element).parent().toggleClass(opt.subMenuParentToggle);
          $($element).prev('ul').slideToggle(opt.toggleSpeed);
          $($element).prev('ul').toggleClass(opt.subMenuToggleClass);
        }
      }

      var expandToggler = '.' + opt.meanExpandClass;
      $(expandToggler).each(function () {
        $(this).on('click', function (e) {
          e.preventDefault();
          toggleDropDown(this);
        });
      });

      $(opt.menuToggleBtn).each(function () {
        $(this).on('click', function () {
          menuToggle();
        });
      });

      menu.on('click', function (e) {
        e.stopPropagation();
        menuToggle();
      });

      menu.find('div').on('click', function (e) {
        e.stopPropagation();
      });
    });
  };

  $('.vs-menu-wrapper').vsmobilemenu();

  /*---------- 04. Sticky fix ----------*/
  var lastScrollTop = '';
  var scrollToTopBtn = '.scrollToTop';

  function stickyMenu($targetMenu, $toggleClass, $parentClass) {
    var st = $(window).scrollTop();
    var height = $targetMenu.css('height');
    $targetMenu.parent().css('min-height', height);
    if ($(window).scrollTop() > 800) {
      $targetMenu.parent().addClass($parentClass);
      if (st > lastScrollTop) {
        $targetMenu.removeClass($toggleClass);
      } else {
        $targetMenu.addClass($toggleClass);
      }
    } else {
      $targetMenu.parent().css('min-height', '').removeClass($parentClass);
      $targetMenu.removeClass($toggleClass);
    }
    lastScrollTop = st;
  }

  $(window).on("scroll", function () {
    stickyMenu($('.sticky-active'), "active", "will-sticky");
    if ($(this).scrollTop() > 500) {
      $(scrollToTopBtn).addClass('show');
    } else {
      $(scrollToTopBtn).removeClass('show');
    }
  });

  /*---------- 05. Scroll To Top ----------*/
  $(scrollToTopBtn).each(function () {
    $(this).on('click', function (e) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: 0 }, lastScrollTop / 3);
      return false;
    });
  });

  /*---------- 06. Set Background Image ----------*/
  if ($('[data-bg-src]').length > 0) {
    $('[data-bg-src]').each(function () {
      var src = $(this).attr('data-bg-src');
      $(this).css('background-image', 'url(' + src + ')');
      $(this).removeAttr('data-bg-src').addClass('background-image');
    });
  }

  /*----------- 08. Magnific Popup ----------*/
  $('.popup-image').magnificPopup({
    type: 'image',
    gallery: { enabled: true }
  });

  $('.popup-video').magnificPopup({
    type: 'iframe'
  });

  /*----------- 09. Filter ----------*/
  $('.filter-active').imagesLoaded(function () {
    var $filter = '.filter-active',
      $filterItem = '.filter-item',
      $filterMenu = '.filter-menu-active';

    if ($($filter).length > 0) {
      var $grid = $($filter).isotope({
        itemSelector: $filterItem,
        filter: '*',
        masonry: {
          columnWidth: $filterItem
        }
      });

      $($filterMenu).on('click', 'button', function () {
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
      });

      $($filterMenu).on('click', 'button', function (event) {
        event.preventDefault();
        $(this).addClass('active').siblings('.active').removeClass('active');
      });
    }
  });

  /*----------- 10. Slick 3D Slider ----------*/
  var slick3d = $('.slick-3d-active');
  slick3d.on('init', function (event, slick) {
    var cur = $(slick.$slides[slick.currentSlide]),
      next = cur.next(),
      next2 = cur.next().next(),
      prev = cur.prev(),
      prev2 = cur.prev().prev();
    prev.addClass('slick-3d-prev');
    next.addClass('slick-3d-next');
    prev2.addClass('slick-3d-prev2');
    next2.addClass('slick-3d-next2');
    cur.removeClass('slick-3d-next slick-3d-prev slick-3d-next2 slick-3d-prev2');
    slick.$prev = prev;
    slick.$next = next;
  }).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
    var cur = $(slick.$slides[nextSlide]);
    slick.$prev.removeClass('slick-3d-prev');
    slick.$next.removeClass('slick-3d-next');
    slick.$prev.prev().removeClass('slick-3d-prev2');
    slick.$next.next().removeClass('slick-3d-next2');
    var next = cur.next(),
      prev = cur.prev();
    prev.addClass('slick-3d-prev');
    next.addClass('slick-3d-next');
    prev.prev().addClass('slick-3d-prev2');
    next.next().addClass('slick-3d-next2');
    slick.$prev = prev;
    slick.$next = next;
    cur.removeClass('slick-3d-next slick-3d-prev slick-3d-next2 slick-3d-prev2');
  });

  slick3d.slick({
    speed: 1000,
    arrows: true,
    dots: false,
    focusOnSelect: true,
    prevArrow: '<button type="button" class="slick-prev"><i class="fal fa-arrow-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="fal fa-arrow-right"></i></button>',
    infinite: true,
    centerMode: true,
    slidesPerRow: 1,
    slidesToShow: 1,
    slidesToScroll: 1,
    centerPadding: '0',
    swipe: true,
    responsive: [{
      breakpoint: 1024,
      settings: { arrows: false }
    }]
  });

  /*----------- 11. Custom Tab ----------*/
  $.fn.vsTab = function (options) {
    var opt = $.extend({
      sliderTab: false,
      tabButton: 'button'
    }, options);

    $(this).each(function () {
      var $menu = $(this);
      var $button = $menu.find(opt.tabButton);
      $menu.append('<span class="indicator"></span>');
      var $line = $menu.find('.indicator');

      $button.on('click', function (e) {
        e.preventDefault();
        var cBtn = $(this);
        cBtn.addClass('active').siblings().removeClass('active');
        if (opt.sliderTab) {
          $(slider).slick('slickGoTo', cBtn.data('slide-go-to'));
        } else {
          linePos();
        }
      });

      if (opt.sliderTab) {
        var slider = $menu.data('asnavfor');
        var i = 0;
        $button.each(function () {
          var slideBtn = $(this);
          slideBtn.attr('data-slide-go-to', i++);
          if (slideBtn.hasClass('active')) {
            $(slider).slick('slickGoTo', slideBtn.data('slide-go-to'));
          }
          $(slider).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            $menu.find(opt.tabButton + '[data-slide-go-to="' + nextSlide + '"]').addClass('active').siblings().removeClass('active');
            linePos();
          });
        });
      }

      function linePos() {
        var $btnActive = $menu.find(opt.tabButton + '.active'),
          height = $btnActive.css('height'),
          width = $btnActive.css('width'),
          top = $btnActive.position().top + 'px',
          left = $btnActive.position().left + 'px';

        $line.get(0).style.setProperty('--height-set', height);
        $line.get(0).style.setProperty('--width-set', width);
        $line.get(0).style.setProperty('--pos-y', top);
        $line.get(0).style.setProperty('--pos-x', left);

        if ($($button).first().position().left === $btnActive.position().left) {
          $line.addClass('start').removeClass('center end');
        } else if ($($button).last().position().left === $btnActive.position().left) {
          $line.addClass('end').removeClass('center start');
        } else {
          $line.addClass('center').removeClass('start end');
        }
      }
      linePos();
    });
  };

  if ($('.vs-slider-tab').length) {
    $('.vs-slider-tab').vsTab({
      sliderTab: true,
      tabButton: '.tab-btn'
    });
  }

  if ($('.recent-post-tab').length) {
    $('.recent-post-tab').vsTab({
      sliderTab: true,
      tabButton: '.nav-link'
    });
  }

  /*----------- 12. Smooth Scroll ----------*/
  SmoothScroll({
    animationTime: 1000,
    stepSize: 100,
    accelerationDelta: 50,
    accelerationMax: 3,
    keyboardSupport: false,
    arrowScroll: 50,
    pulseAlgorithm: true,
    pulseScale: 4,
    pulseNormalize: 1,
    touchpadSupport: false,
    fixedBackground: true,
    excluded: 'a[href="#"], .btn, button'
  });

  /*----------- 13. Custom Cursor ----------*/
  $.fn.vsCursor = function () {
    var cursor = $(this);
    let cursorPositon = { x: 0, y: 0, targetX: 0, targetY: 0 };

    $(document).on("mousemove", function (e) {
      cursorPositon.targetX = e.pageX;
      cursorPositon.targetY = e.pageY;
    });

    function positionSet() {
      cursorPositon.x += 0.2 * (cursorPositon.targetX - cursorPositon.x);
      cursorPositon.y += 0.2 * (cursorPositon.targetY - cursorPositon.y);
      cursor.css({
        'transform': 'translate(' + Math.floor(cursorPositon.x - 5) + 'px, ' + Math.floor(cursorPositon.y - 5) + 'px)'
      });
      requestAnimationFrame(positionSet);
    }
    positionSet();
  };

  $('.vs-cursor').vsCursor();

  /*---------- 14. Popup Sidemenu ----------*/
  function popupSideMenu($sideMenu, $sideMunuOpen, $sideMenuCls, $toggleCls) {
    $($sideMunuOpen).on('click', function (e) {
      e.preventDefault();
      $($sideMenu).addClass($toggleCls);
    });
    $($sideMenu).on('click', function (e) {
      e.stopPropagation();
      $($sideMenu).removeClass($toggleCls);
    });
    var sideMenuChild = $sideMenu + ' > div';
    $(sideMenuChild).on('click', function (e) {
      e.stopPropagation();
      $($sideMenu).addClass($toggleCls);
    });
    $($sideMenuCls).on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $($sideMenu).removeClass($toggleCls);
    });
  }
  popupSideMenu('.sidemenu-wrapper', '.sideMenuToggler', '.sideMenuCls', 'show');

  /*---------- 15. Search Box Popup ----------*/
  function popupSarchBox($searchBox, $searchOpen, $searchCls, $toggleCls) {
    $($searchOpen).on('click', function (e) {
      e.preventDefault();
      $($searchBox).addClass($toggleCls);
    });
    $($searchBox).on('click', function (e) {
      e.stopPropagation();
      $($searchBox).removeClass($toggleCls);
    });
    $($searchBox).find('form').on('click', function (e) {
      e.stopPropagation();
      $($searchBox).addClass($toggleCls);
    });
    $($searchCls).on('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      $($searchBox).removeClass($toggleCls);
    });
  }
  popupSarchBox('.popup-search-box', '.searchBoxTggler', '.searchClose', 'show');

  /*---------- 16. Parallax Effect ----------*/
  new universalParallax().init();
  if ($('.parallax').length) {
    $('.parallax').each(function () {
      var bgCls = $(this).data('bg-class');
      $(this).parent().addClass(bgCls);
    });
  }

  /*----------- 17. Custom Slider Animaiton ----------*/
  $('[data-ani-duration]').each(function () {
    var durationTime = $(this).data('ani-duration');
    $(this).css('animation-duration', durationTime);
  });

  $('[data-ani-delay]').each(function () {
    var delayTime = $(this).data('ani-delay');
    $(this).css('animation-delay', delayTime);
  });

  $('[data-ani]').each(function () {
    var animaionName = $(this).data('ani');
    $(this).addClass(animaionName);
    $('.slick-current [data-ani]').addClass('vs-animated');
  });

  $('.vs-carousel').on('afterChange', function (event, slick, currentSlide) {
    $(slick.$slides).find('[data-ani]').removeClass('vs-animated');
    $(slick.$slides[currentSlide]).find('[data-ani]').addClass('vs-animated');
  });

  /*----------- 18. Breaking News Slider ----------*/
  $('.breaking-news-slider').slick({
    dots: false,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    prevArrow: '<button type="button" class="slick-prev"><i class="far fa-arrow-left"></i></button>',
    nextArrow: '<button type="button" class="slick-next"><i class="far fa-arrow-right"></i></button>',
    vertical: true,
    responsive: [{
      breakpoint: 1199,
      settings: { arrows: false }
    }]
  });

  /*----------- 19. Slider On Tab ----------*/
  $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
    var targetTab = $(this).data('bs-target');
    $(targetTab).find('.vs-carousel').slick('refresh');
  });


  // 19. Запрещаем браузеру самому скроллить при загрузке
  window.addEventListener('load', function () {
    setTimeout(function () {
      history.scrollRestoration = 'manual';
    }, 0);
  });

  // 20. Сохраняем и восстанавливаем позицию скролла
  (function () {
    const key = 'scrollPos_' + location.pathname;
    const saved = sessionStorage.getItem(key);
    if (saved) {
      window.scrollTo(0, parseInt(saved, 10));
    }

    window.addEventListener('beforeunload', () => {
      sessionStorage.setItem(key, window.scrollY);
    });
  })();

})(jQuery);
/*  Глобальное сохранение / восстановление прокрутки между страницами  */
(function () {
  const GLOBAL_KEY = 'globalScrollPos';

  // 21. Сохраняем текущую позицию при уходе С ЛЮБОЙ страницы
  window.addEventListener('beforeunload', function () {
    const data = JSON.parse(localStorage.getItem(GLOBAL_KEY) || '{}');
    data[location.href] = window.scrollY;
    localStorage.setItem(GLOBAL_KEY, JSON.stringify(data));
  });

  // 22. Восстанавливаем СРАЗУ, как только DOM доступен
  (function restoreScroll() {
    const data = JSON.parse(localStorage.getItem(GLOBAL_KEY) || '{}');
    const saved = data[location.href];
    if (typeof saved === 'number') {
      window.scrollTo(0, saved);
    }
  })();

  // 23. Отключаем браузерное восстановление, чтобы не мешало
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
})();
/*  «Возврат на ту же высоту» для кабинетного меню  */
(function () {
  const STORAGE_KEY = 'cabScroll_' + location.pathname; // уникально для каждого раздела

  /* 24. Сохраняем позицию, когда человек покидает страницу */
  window.addEventListener('beforeunload', function () {
    localStorage.setItem(STORAGE_KEY, window.scrollY);
  });

  /* 25. Восстанавливаем сразу, как только DOM готов */
  document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved !== null) {
      window.scrollTo(0, parseInt(saved, 10));
    }
  });

  /* 26. Отключаем браузерное поведение, чтобы не мешало */
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
})();
/*  ПЕРЕХОДЫ ПО ВСЕМ ССЫЛКАМ С СОХРАНЕНИЕМ ВЫСОТЫ  */
(function () {
  const GLOBAL_KEY = 'globalScrollOnClick'; // одно поле для всех страниц

  // 27. Перехватываем клик по ЛЮБОЙ ссылке
  $(document).on('click', 'a[href]:not([href^="#"])', function (e) {
    // сохраняем текущую прокрутку В МОМЕНТ клика
    localStorage.setItem(GLOBAL_KEY, window.scrollY);
    // разрешаем переход – браузер сам уйдёт на новый url
  });

  // 28. На новой странице восстанавливаем высоту до первого paint
  (function restoreScroll() {
    const saved = localStorage.getItem(GLOBAL_KEY);
    if (saved !== null) {
      window.scrollTo(0, parseInt(saved, 10));
      // по желанию: удаляем, чтобы не мешать дальнейшим переходам
      // localStorage.removeItem(GLOBAL_KEY);
    }
  })();

  // 29. Отключаем браузерное «восстановление» scroll
  if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
  }
})();