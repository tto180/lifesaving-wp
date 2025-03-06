(()=>{"use strict";var e={5792:(e,t,a)=>{a.d(t,{c:()=>l});var s=a(1280),r=a.n(s),n=a(3396);class o extends r().Component{constructor(e){super(e)}render(){let e="",t="";return 1==this.props.text&&(t=(0,s.createElement)("span",{className:"supporting-text"},(0,n.__)("Your report is being generated.","learndash-reports-pro"))),e=(0,s.createElement)("div",{className:"wisdm-learndash-reports-chart-block"},(0,s.createElement)("div",{className:"wisdm-learndash-reports-revenue-from-courses graph-card-container"},(0,s.createElement)("div",{className:"wisdm-graph-loading"},(0,s.createElement)("img",{src:wisdm_learndash_reports_front_end_script_total_revenue_earned.plugin_asset_url+"/images/loader.svg"}),t))),e}}const l=o},3624:(e,t,a)=>{a.d(t,{c:()=>p});var s=a(1280),r=a.n(s),n=a(5792);const o=window.moment;var l=a.n(o),c=a(3396);const d=window.wp.element;class i extends s.Component{constructor(e){super(e),this.state={isLoaded:!1,error:null,start_date:l()(new Date(wisdm_ld_reports_common_script_data.start_date)).unix(),end_date:l()(new Date(wisdm_ld_reports_common_script_data.end_date)).unix()},this.durationUpdated=this.durationUpdated.bind(this),this.updateBlock=this.updateBlock.bind(this)}durationUpdated(e){this.setState({start_date:e.detail.startDate,end_date:e.detail.endDate}),this.updateBlock()}componentDidMount(){document.addEventListener("duration_updated",this.durationUpdated),this.updateBlock()}updateBlock(e="/rp/v1/total-courses"){let t="/rp/v1/total-courses?start_date="+this.state.start_date+"&end_date="+this.state.end_date;wisdm_ld_reports_common_script_data.wpml_lang&&(t+="&wpml_lang="+wisdm_ld_reports_common_script_data.wpml_lang),wp.apiFetch({path:t}).then((e=>{const t=e.percentChange;let a="udup",s="change-value",r="",n="",o="";0<t?(a="udup",s="change-value-positive",n=(0,c.__)("Up","learndash-reports-pro"),o=wisdm_learndash_reports_front_end_script_total_revenue_earned.plugin_asset_url+"/images/up.png"):0>t?(a="uddown",s="change-value-negative",n=(0,c.__)("Down","learndash-reports-pro"),o=wisdm_learndash_reports_front_end_script_total_revenue_earned.plugin_asset_url+"/images/down.png"):0==t&&(r="wrld-hidden",n=(0,c.__)("Up","learndash-reports-pro"),o=wisdm_learndash_reports_front_end_script_total_revenue_earned.plugin_asset_url+"/images/up.png"),this.setState({isLoaded:!0,graphData:{totalCourses:e.totalCourses,percentChange:t+"%",chnageDirectionClass:a,percentValueClass:s,hideChange:r,udtxt:n,udsrc:o},startDate:l().unix(e.requestData.start_date).format("MMM, DD YYYY"),endDate:l().unix(e.requestData.end_date).format("MMM, DD YYYY")})})).catch((e=>{this.setState({error:e,graph_summary:[],isLoaded:!0,series:[]})}))}render(){let e=(0,s.createElement)("div",null);return e=this.state.isLoaded?this.state.error?(0,s.createElement)("div",{className:"wisdm-learndash-reports-chart-block error"},(0,s.createElement)("div",null,this.state.error.message)):(0,s.createElement)("div",{className:"wisdm-learndash-reports-chart-block"},(0,s.createElement)("div",{className:"total-courses-container top-card-container"},(0,s.createElement)("div",{className:"wrld-date-filter"},(0,s.createElement)("span",{className:"dashicons dashicons-calendar-alt"}),(0,s.createElement)("div",{className:"wdm-tooltip"},(0,c.__)("Date filter applied:","learndash-reports-pro"),(0,s.createElement)("br",null),this.state.startDate," -"," ",this.state.endDate)),(0,s.createElement)("div",{className:"total-courses-icon"},(0,s.createElement)("img",{src:wisdm_learndash_reports_front_end_script_total_courses.plugin_asset_url+"/images/icon_course_counter.png"})),(0,s.createElement)("div",{className:"total-courses-details"},(0,s.createElement)("div",{className:"total-courses-text top-label-text"},(0,s.createElement)("span",null,(0,c.__)("Total","learndash-reports-pro")+" "+wisdm_reports_get_ld_custom_lebel_if_avaiable("courses"))),(0,s.createElement)("div",{className:"total-courses-figure"},(0,s.createElement)("span",null,this.state.graphData.totalCourses)),(0,s.createElement)("div",{className:`total-courses-percent-change ${this.state.graphData.hideChange}`},(0,s.createElement)("span",{className:this.state.graphData.chnageDirectionClass},(0,s.createElement)("img",{src:this.state.graphData.udsrc})),(0,s.createElement)("span",{className:this.state.graphData.percentValueClass},this.state.graphData.percentChange),(0,s.createElement)("span",{className:"ud-txt"},this.state.graphData.udtxt))))):(0,s.createElement)(n.c,null),e}}const p=i;document.addEventListener("DOMContentLoaded",(function(e){const t=document.getElementsByClassName("wisdm-learndash-reports-total-courses front");t.length>0&&(0,d.createRoot)(t[0]).render(r().createElement(i))}))},1280:e=>{e.exports=window.React},3396:e=>{e.exports=window.wp.i18n}},t={};function a(s){var r=t[s];if(void 0!==r)return r.exports;var n=t[s]={exports:{}};return e[s](n,n.exports,a),n.exports}a.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return a.d(t,{a:t}),t},a.d=(e,t)=>{for(var s in t)a.o(t,s)&&!a.o(e,s)&&Object.defineProperty(e,s,{enumerable:!0,get:t[s]})},a.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e=a(1280);const t=window.wp.blocks;var s=a(3396);a(5792);const r=window.wp.blockEditor;var n=a(3624);const o=(0,e.createElement)("svg",{version:"1.0",xmlns:"http://www.w3.org/2000/svg",width:"24.000000pt",height:"24.000000pt",viewBox:"0 0 24.000000 24.000000",preserveAspectRatio:"xMidYMid meet"},(0,e.createElement)("path",{d:"M9.2,6.9C9,6.8,8.9,6.8,8.7,6.8l0,0C8.3,6.7,8,7.2,8,7.4v5.7c0,0.3,0.2,0.5,0.3,0.6s0.3,0.1,0.4,0.1c0.1,0,0.2,0,0.3-0.1 l4.3-2.9c0.3-0.2,0.5-0.3,0.5-0.5s-0.1-0.3-0.4-0.5L9.2,6.9z M11.8,10.3l-2.5,1.5V8.7L11.8,10.3z"}),(0,e.createElement)("path",{d:"M22.3,10.7h-7.2c-0.7,0-1.3,0.6-1.3,1.3v10c0,0.7,0.6,1.3,1.3,1.3h7.2c0.7,0,1.3-0.6,1.3-1.3V12 C23.6,11.3,23,10.7,22.3,10.7z M22.5,12v10c0,0.1-0.2,0.3-0.3,0.3H15c-0.1,0-0.3-0.2-0.3-0.3V12c0-0.1,0.2-0.3,0.3-0.3h7.2 C22.4,11.7,22.5,11.9,22.5,12z"}),(0,e.createElement)("path",{d:"M17.7,17.5H16c-0.3,0-0.5,0.2-0.5,0.5s0.2,0.5,0.5,0.5h1.6c0.3,0,0.5-0.2,0.5-0.5C18.2,17.7,17.9,17.5,17.7,17.5z"}),(0,e.createElement)("path",{d:"M17.7,19.7H16c-0.3,0-0.5,0.2-0.5,0.5s0.2,0.5,0.5,0.5h1.6c0.3,0,0.5-0.2,0.5-0.5C18.2,19.9,17.9,19.7,17.7,19.7z"}),(0,e.createElement)("path",{d:"M21.3,17.5h-1.6c-0.3,0-0.5,0.2-0.5,0.5s0.2,0.5,0.5,0.5h1.6c0.3,0,0.5-0.2,0.5-0.5S21.5,17.5,21.3,17.5z"}),(0,e.createElement)("path",{d:"M21.3,19.7h-1.6c-0.3,0-0.5,0.2-0.5,0.5s0.2,0.5,0.5,0.5h1.6c0.3,0,0.5-0.2,0.5-0.5C21.8,19.9,21.5,19.7,21.3,19.7z"}),(0,e.createElement)("path",{d:"M22.6,3.8c-0.1-1.7-1.4-3.1-3.1-3.3l0,0H3.4C1.6,0.7,0.3,2,0.3,3.8v13.7c0,1.9,1.6,3.5,3.5,3.5H12c0.5,0,0.8-0.3,0.8-0.8 s-0.3-0.8-0.8-0.8H3.7c-1,0-1.9-0.8-1.9-1.9V3.8c0-1,0.8-1.9,1.9-1.9h15.5c1,0,1.9,0.8,1.9,1.9v5c0,0.5,0.3,0.8,0.8,0.8 s0.8-0.3,0.8-0.8L22.6,3.8z"}),(0,e.createElement)("g",null,(0,e.createElement)("path",{d:"M21.6,15.8h-5.8c-0.1,0-0.2-0.1-0.2-0.2v-2.1c0-0.1,0.1-0.2,0.2-0.2h5.8c0.1,0,0.2,0.1,0.2,0.2v2.1 C21.8,15.7,21.7,15.8,21.6,15.8z M16,15.3h5.3v-1.6H16V15.3z"})));(0,t.registerBlockType)("wisdm-learndash-reports/total-courses",{title:(0,s.__)("Total Courses","learndash-reports-pro"),description:(0,s.__)("Displays Count of the courses","learndash-reports-pro"),category:"wisdm-learndash-reports",className:"learndash-reports-by-wisdmlabs-total-courses",icon:o,attributes:{blockContent:{type:"html",default:""}},edit:t=>(0,e.createElement)("div",{...(0,r.useBlockProps)()},(0,e.createElement)("div",{className:"wisdm-learndash-reports-total-courses"},(0,e.createElement)(n.c,null))),save:()=>(0,e.createElement)("div",{...r.useBlockProps.save()},(0,e.createElement)("div",{className:"wisdm-learndash-reports-total-courses front"}))})})()})();