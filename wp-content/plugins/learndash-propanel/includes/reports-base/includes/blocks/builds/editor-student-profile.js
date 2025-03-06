(()=>{"use strict";var e={8856:(e,t,r)=>{r.d(t,{c:()=>i});var s=r(1280),a=r.n(s),n=r(3396);class l extends a().Component{constructor(e){super(e)}render(){let e="",t="";return 1==this.props.text&&(t=(0,s.createElement)("span",{className:"supporting-text"},(0,n.__)("Your report is being generated.","learndash-reports-pro"))),e=(0,s.createElement)("div",{className:"wisdm-learndash-reports-chart-block"},(0,s.createElement)("div",{className:"wisdm-learndash-reports-revenue-from-courses graph-card-container"},(0,s.createElement)("div",{className:"wisdm-graph-loading"},(0,s.createElement)("img",{src:wisdm_learndash_reports_front_end_script_total_revenue_earned.plugin_asset_url+"/images/loader.svg"}),t))),e}}const o=l;var c=r(8496);class d extends s.Component{constructor(e){super(e);let t=null;const r=this.getUserType()?"www.gravatar.com/avatar/789047b2eb7fd33f3fb6858358dcc5d8?s=150&r=g&d=mm":wisdm_learndash_reports_front_end_script_student_table.avatar_url,s=this.getUserType()?"No Name":wisdm_learndash_reports_front_end_script_student_table.current_user.data.display_name;this.getUserType()&&(t={message:(0,n.__)("Sorry you are not allowed to access this block, please check if you have proper access permissions","learndash-reports-by-wisdmlabs")}),this.state={isLoaded:!0,userImage:r,userName:s,error:t}}getUserType(){return 0==wisdm_learndash_reports_front_end_script_student_table.current_user.ID}componentDidMount(){}componentDidUpdate(){}render(){let e=(0,s.createElement)("div",null);return e=this.state.error?"":this.state.isLoaded?(0,s.createElement)("div",{className:"user-info-section"},(0,s.createElement)("div",{className:"thumbnail"},(0,s.createElement)("img",{alt:"",src:this.state.userImage,srcSet:this.state.userImage,className:"avatar avatar-96 photo",height:"96",width:"96",loading:"lazy",decoding:"async"})),(0,s.createElement)("div",{className:"information"},(0,s.createElement)("div",{className:"label clabel"},(0,s.createElement)("span",null,"Student Name")),(0,s.createElement)("div",{className:"name"},(0,s.createElement)("span",null,this.state.userName)))):(0,s.createElement)(o,null),e}}const i=d;document.addEventListener("DOMContentLoaded",(function(e){const t=document.getElementsByClassName("wisdm-learndash-reports-student-profile front");t.length>0&&(0,c.createRoot)(t[0]).render(a().createElement(d))}))},1280:e=>{e.exports=window.React},8496:e=>{e.exports=window.wp.element},3396:e=>{e.exports=window.wp.i18n}},t={};function r(s){var a=t[s];if(void 0!==a)return a.exports;var n=t[s]={exports:{}};return e[s](n,n.exports,r),n.exports}r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var s in t)r.o(t,s)&&!r.o(e,s)&&Object.defineProperty(e,s,{enumerable:!0,get:t[s]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{const e=window.wp.blocks;var t=r(3396),s=r(8496);const a=window.wp.blockEditor,n=window.wp.hooks;r(1280);var l=r(8856);(0,n.createHooks)();const o=(0,s.createElement)("svg",{version:"1.0",xmlns:"http://www.w3.org/2000/svg",width:"24.000000pt",height:"24.000000pt",viewBox:"0 0 24.000000 24.000000",preserveAspectRatio:"xMidYMid meet"},(0,s.createElement)("g",null,(0,s.createElement)("path",{d:"M9.5,12.1c-0.3-0.2-1,0-1.1,0.2L7,13.6l-1.3-1.3c-0.2-0.3-0.9-0.4-1.2-0.1c-0.3,0.4-0.1,1,0.1,1.1l1.3,1.3L4.6,16 c-0.2,0.2-0.4,0.7,0,1.1c0.3,0.4,0.9,0.1,1.1-0.1L7,15.8l1.3,1.3c0.2,0.2,0.8,0.3,1.2,0c0.3-0.3,0.1-1-0.1-1.1l-1.3-1.3l1.3-1.3 C9.7,13.2,9.9,12.5,9.5,12.1z M5.4,16.9L5.4,16.9L5.4,16.9L5.4,16.9z"}),(0,s.createElement)("path",{d:"M10.2,4.7C10,4.6,9.9,4.5,9.5,4.5C9.2,4.5,9,4.7,8.9,4.8L6,7.7l-1-1c-0.3-0.3-0.9-0.3-1.2,0C3.4,7,3.4,7.5,3.8,7.9l1.7,1.6 c0.2,0.2,0.4,0.3,0.6,0.3s0.4-0.1,0.6-0.3L10.2,6c0.2-0.2,0.3-0.4,0.3-0.6S10.4,4.9,10.2,4.7z"})),(0,s.createElement)("path",{d:"M12,19.3H3.5c-1,0-1.8-0.8-1.8-1.8V4.8c0-1,0.8-1.8,1.8-1.8h14.4c1,0,1.8,0.8,1.8,1.8v4.7c0,0.4,0.3,0.8,0.8,0.8 s0.8-0.3,0.8-0.8V4.8c-0.1-1.7-1.4-3-2.9-3.1l0,0h-15C1.6,1.8,0.2,3.2,0.2,4.8v12.8c0,1.8,1.5,3.2,3.2,3.2h8.5 c0.4,0,0.8-0.3,0.8-0.8C12.7,19.7,12.4,19.3,12,19.3z"}),(0,s.createElement)("g",null,(0,s.createElement)("path",{d:"M15.9,19.6L15.9,19.6c-0.2-0.1-0.4-0.4-0.1-0.6l3.4-6c0.1-0.2,0.4-0.4,0.6-0.1l0,0c0.2,0.1,0.4,0.4,0.1,0.6l-3.4,5.9 C16.4,19.6,16.2,19.7,15.9,19.6z"}),(0,s.createElement)("path",{d:"M15.3,13.5c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5c0.8,0,1.5-0.7,1.5-1.5C16.9,14.2,16.2,13.5,15.3,13.5z"}),(0,s.createElement)("path",{d:"M20.3,16.2c-0.8,0-1.5,0.7-1.5,1.5s0.7,1.5,1.5,1.5s1.5-0.7,1.5-1.5S21.2,16.2,20.3,16.2z"})),(0,s.createElement)("path",{d:"M17.8,22.1c-3.3,0-6-2.7-6-6c0-3.3,2.7-6,6-6s6,2.7,6,6C23.7,19.5,21.1,22.1,17.8,22.1z M17.8,11c-2.9,0-5.2,2.3-5.2,5.2 s2.3,5.2,5.2,5.2S23,19,23,16.2S20.6,11,17.8,11z"}));(0,e.registerBlockType)("wisdm-learndash-reports/student-profile",{title:(0,t.__)("Student Profile","learndash-reports-pro"),description:(0,t.__)("Student Profile","learndash-reports-pro"),category:"wisdm-learndash-reports",className:"learndash-reports-by-wisdmlabs-student-dashboard",icon:o,attributes:{},edit:e=>(0,s.createElement)("div",{...(0,a.useBlockProps)()},(0,s.createElement)(l.c,null)),save:()=>(0,s.createElement)("div",{...a.useBlockProps.save()},(0,s.createElement)("div",{className:"wisdm-learndash-reports-student-profile front"}))})})()})();