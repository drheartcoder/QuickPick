  function showProcessingOverlay()
  {
    var doc_height = $(document).height();
    var doc_width  = $(document).width();
    var spinner_html = "";

    // spinner_html += '<div class="sk-cube-grid">';
      spinner_html += '<div class="sk-cube sk-cube1"></div>';
      spinner_html += '<div class="sk-cube sk-cube2"></div>';
      spinner_html += '<div class="sk-cube sk-cube3"></div>';
      spinner_html += '<div class="sk-cube sk-cube4"></div>';
      spinner_html += '<div class="sk-cube sk-cube5"></div>';
      spinner_html += '<div class="sk-cube sk-cube6"></div>';
      spinner_html += '<div class="sk-cube sk-cube7"></div>';
      spinner_html += '<div class="sk-cube sk-cube8"></div>';
      spinner_html += '<div class="sk-cube sk-cube9"></div>';
    // spinner_html += '</div>';

    // spinner_html += '<div class="sk-cube1 sk-cube"></div>';
    // spinner_html += '<div class="sk-cube2 sk-cube"></div>';
    // spinner_html += '<div class="sk-cube4 sk-cube"></div>';
    // spinner_html += '<div class="sk-cube3 sk-cube"></div>';

     // $("body").append("<div id='global_processing_overlay'><div class='sk-folding-cube'>"+spinner_html+"</div></div>");
     $("body").append("<div id='global_processing_overlay'><div class='sk-cube-grid'>"+spinner_html+"</div></div>");

     $("#global_processing_overlay").height(doc_height)
                                   .css({
                                     'opacity' : 0.9,
                                     'position': 'fixed',
                                     'top': 0,
                                     'left': 0,
                                     'background-color': '#1D1D1D',
                                     'width': '100%',
                                     'z-index': 2147483647,
                                     'text-align': 'center',
                                     'vertical-align': 'middle',
                                     'margin': 'auto',
                                   }); 

      $("body").addClass("notifications-fix");
  }

function hideProcessingOverlay()
{
  $("body").removeClass("notifications-fix");
  $("#global_processing_overlay").remove();
}