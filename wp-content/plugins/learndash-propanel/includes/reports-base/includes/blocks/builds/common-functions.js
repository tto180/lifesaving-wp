(()=>{function e(){jQuery(".wrld-mw-1400").removeClass("wrld-xl"),jQuery(".wrld-mw-1400").removeClass("wrld-lg"),jQuery(".wrld-mw-1400").removeClass("wrld-m"),jQuery(".wrld-mw-1400").removeClass("wrld-s"),jQuery(".wrld-mw-1400").removeClass("wrld-xs");const e=jQuery(".wrld-mw-1400").width();e>1199?jQuery(".wrld-mw-1400").addClass("wrld-xl"):e>992?jQuery(".wrld-mw-1400").addClass("wrld-lg"):e>768?jQuery(".wrld-mw-1400").addClass("wrld-m"):e>584?jQuery(".wrld-mw-1400").addClass("wrld-s"):jQuery(".wrld-mw-1400").addClass("wrld-xs")}document.addEventListener("DOMContentLoaded",(function(t){if(document.querySelectorAll(".wrld-mw-1400").length>0){new ResizeObserver(e).observe(document.querySelectorAll(".wrld-mw-1400")[0]);const t=jQuery(".wrld-mw-1400").width();jQuery(".wrld-mw-1400").removeClass("wrld-xl"),jQuery(".wrld-mw-1400").removeClass("wrld-lg"),jQuery(".wrld-mw-1400").removeClass("wrld-m"),jQuery(".wrld-mw-1400").removeClass("wrld-s"),jQuery(".wrld-mw-1400").removeClass("wrld-xs"),t>1199?jQuery(".wrld-mw-1400").addClass("wrld-xl"):t>992?jQuery(".wrld-mw-1400").addClass("wrld-lg"):t>768?jQuery(".wrld-mw-1400").addClass("wrld-m"):t>584?jQuery(".wrld-mw-1400").addClass("wrld-s"):jQuery(".wrld-mw-1400").addClass("wrld-xs")}if(void 0!==wp.data&&(wisdm_ld_reports_common_script_data.page_configuration_status&&0==document.getElementsByClassName("wrld-notice").length&&window.wp.data.dispatch("core/notices").createNotice("success",'<div class="wrld-notice"><span>'+wisdm_ld_reports_common_script_data.notice_content.header+"</span><ul> <li>"+wisdm_ld_reports_common_script_data.notice_content.li_1+"</li> <li>"+wisdm_ld_reports_common_script_data.notice_content.li_2+"</li><li>"+wisdm_ld_reports_common_script_data.notice_content.li_3+"</li><ul></div>",{__unstableHTML:!0,isDismissible:!0}),wisdm_ld_reports_common_script_data.page_student_configuration_status&&0==document.getElementsByClassName("wrld-notice").length&&window.wp.data.dispatch("core/notices").createNotice("success",'<div class="wrld-notice"><span>'+wisdm_ld_reports_common_script_data.notice_student_content.header+"</span><ul> <li>"+wisdm_ld_reports_common_script_data.notice_student_content.li_1+"</li> <li>"+wisdm_ld_reports_common_script_data.notice_student_content.li_2+"</li><ul></div>",{__unstableHTML:!0,isDismissible:!0}),void 0!==wp.data&&void 0!==wp.data.select("core/editor"))){const{isSavingPost:e}=wp.data.select("core/editor");let t=!0,d=!0;wp.data.subscribe((()=>{e()?t=!1:t||("publish"!=wp.data.select("core/editor").getEditedPostAttribute("status")||wisdm_ld_reports_common_script_data.dashboard_page_id!=wp.data.select("core/editor").getEditedPostAttribute("id")||0!=wisdm_ld_reports_common_script_data.visited_dashboard&&"free"!=wisdm_ld_reports_common_script_data.visited_dashboard||(window.location.href=wp.data.select("core/editor").getEditedPostAttribute("link")),t=!0)})),wp.data.subscribe((()=>{e()?d=!1:d||("publish"==wp.data.select("core/editor").getEditedPostAttribute("status")&&wisdm_ld_reports_common_script_data.student_page_id==wp.data.select("core/editor").getEditedPostAttribute("id")&&0==wisdm_ld_reports_common_script_data.visited_student_dashboard&&(window.location.href=wp.data.select("core/editor").getEditedPostAttribute("link")),d=!0)}))}}))})();