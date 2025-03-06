(()=>{"use strict";var e={n:t=>{var a=t&&t.__esModule?()=>t.default:()=>t;return e.d(a,{a}),a},d:(t,a)=>{for(var l in a)e.o(a,l)&&!e.o(t,l)&&Object.defineProperty(t,l,{enumerable:!0,get:a[l]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t)};const t=window.React,a=window.wp.blocks,l=window.wp.element,c=window.wp.apiFetch;var r=e.n(c);const i=window.wp.blockEditor,s=window.wp.components,n=(0,t.createElement)(s.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 64 64"},(0,t.createElement)(s.Path,{fill:"none",d:"M0 0h64v64H0z"}),(0,t.createElement)(s.G,{fillRule:"evenodd"},(0,t.createElement)(s.Path,{fill:"#008bf6",d:"M31.17 14.58A24.71 24.71 0 1 1 6.46 39.29a24.7 24.7 0 0 1 24.71-24.71Z"}),(0,t.createElement)(s.Path,{fill:"#57b6ff",d:"M19.88 60.35a3.7 3.7 0 0 1 1.75.2 23.92 23.92 0 0 0 26.46-5.77 2.67 2.67 0 0 1 1.85-.72 23.88 23.88 0 0 1-18.78 9.12 23.59 23.59 0 0 1-11.28-2.83Z"}),(0,t.createElement)(s.Path,{fill:"#006cbf",d:"M31.21 14.58c.75 0 1.5.05 2.25.11a4.49 4.49 0 0 1-1.79 0 2.6 2.6 0 0 1-.46-.11ZM16.77 59.37a24.65 24.65 0 0 1-8.93-11.92h.51a4.09 4.09 0 0 1 .78 0 4 4 0 0 1 1-2.46 3.07 3.07 0 0 1 1.09-.53 4.66 4.66 0 0 1-1.42-2.9c0-.39-.05-.79-.06-1.21L7.21 39a2.13 2.13 0 0 1-.73-.56 24.55 24.55 0 0 1 2.83-10.67l2.21-.19a38.05 38.05 0 0 1 1.84-5.41A24.93 24.93 0 0 1 18.61 18c.61-.11 1.2-.18 1.73-.23a43.66 43.66 0 0 1 7.41.08c.9.07 1.79.18 2.68.28a7.25 7.25 0 0 1 .39-.88c.61-1.17 3.89-.76 5.44-.48a7.19 7.19 0 0 0-.43-1.77c.56.1 1.1.23 1.65.36a9.07 9.07 0 0 1 .26 1.71c1.64.4 4.23 1.19 4.36 2.36v1q1.31.34 2.58.75A48.12 48.12 0 0 1 49.77 23a24.72 24.72 0 0 1 4.28 25.64l-1.15.1c-.16.38-.35.76-.51 1.1-.91 1.86-3.24 2.61-5.14 2.95a34.41 34.41 0 0 1-7.92.27 74.41 74.41 0 0 1-9.4-1.24 74.23 74.23 0 0 1-9.19-2.38 39.13 39.13 0 0 1-5.21-2.1v4.31a5.07 5.07 0 0 1 1.8 2 7 7 0 0 0 4-1.68c.61-.5 2.34.24 1.63 2.31a7 7 0 0 1-6.19 5.09Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M31.17 19.78a19.5 19.5 0 1 1-19.51 19.5 19.51 19.51 0 0 1 19.51-19.5Z"}),(0,t.createElement)(s.G,{"data-name":"Arm - Right"},(0,t.createElement)(s.Path,{fill:"#385661",d:"M22.18 48.65a7.37 7.37 0 0 1-8.48 1.29l-2.78 5.55C14.89 57.68 22 58 24 51c.69-2.23-1.2-2.91-1.82-2.35Z"}),(0,t.createElement)(s.Path,{fill:"#385661",d:"M22.72 49.34a8.15 8.15 0 0 1-8.6 1.71l-2 4.05a10.84 10.84 0 0 0 4.51.89 6.68 6.68 0 0 0 6.6-5.21 1.38 1.38 0 0 0-.11-1.25.52.52 0 0 0-.4-.19Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M20.06 51a8.24 8.24 0 0 1-5.95 0l-.83 1.68a7.07 7.07 0 0 0 6.81-.68c.52-.42.4-1-.03-1Z"})),(0,t.createElement)(s.G,{"data-name":"Arm - Left"},(0,t.createElement)(s.Path,{fill:"#385661",d:"M37.09 52a9.85 9.85 0 0 1 3.48 5.68l4.72-1.29c-1.22-3.54-4.75-7-8.19-7.32Z"}),(0,t.createElement)(s.Path,{fill:"#385661",d:"M38 51.57a10.7 10.7 0 0 1 3.23 5l2.92-.79a11.32 11.32 0 0 0-6-6Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"m42.75 56.15 1.36-.37A12.64 12.64 0 0 0 40.23 51c-.19.16-.1.55.11.78a10 10 0 0 1 2.41 4.37Z"})),(0,t.createElement)(s.Path,{fill:"#598291",d:"M24.09 57.46c0-.4-.07-.81-.1-1.22a1.69 1.69 0 0 1 .76-1.69 13.94 13.94 0 0 1 3.9-1.27l1-.22h.32l1 .22a14 14 0 0 1 3.89 1.27 1.69 1.69 0 0 1 .76 1.69c0 .47-.06.94-.11 1.41 0 .21 0 .43-.06.65a19.71 19.71 0 0 1-4.37.49 19.32 19.32 0 0 1-6.99-1.33Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M24.91 57.76c-.06-.51-.09-1-.13-1.58a.92.92 0 0 1 .4-1 18.16 18.16 0 0 1 4.69-1.38 18.07 18.07 0 0 1 4.68 1.38.93.93 0 0 1 .39 1c0 .81-.1 1.51-.2 2.28a20 20 0 0 1-3.56.33 19.26 19.26 0 0 1-6.27-1.03Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M26 54.82a30.12 30.12 0 0 1 3.84-1v4.93a18.87 18.87 0 0 1-3.57-.58L26 54.82Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M21.64 56.32c-.19-1-.35-2.09-.46-3.15-.06-.71-.11-1.42-.16-2.13a2.53 2.53 0 0 1 1.15-2.55 21.76 21.76 0 0 1 5.83-1.9l1.58-.34h.49l1.57.34a22.17 22.17 0 0 1 5.87 1.9A2.52 2.52 0 0 1 38.68 51c0 .71-.09 1.42-.16 2.13a35.74 35.74 0 0 1-.73 4.48 19.48 19.48 0 0 1-16.15-1.33Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M23 57a35.38 35.38 0 0 1-.78-6 1.4 1.4 0 0 1 .61-1.47c1.36-.86 4.38-1.54 7.06-2.11 2.66.57 5.69 1.25 7.06 2.11a1.39 1.39 0 0 1 .6 1.47 34.86 34.86 0 0 1-1 6.93s0 .1-.07.19A19.6 19.6 0 0 1 23 57Z"}),(0,t.createElement)(s.Path,{fill:"#7eaaba",d:"M24.65 57.67a43 43 0 0 1-.72-6.67 1.6 1.6 0 0 1 .45-1.48c1.05-.85 3.4-1.53 5.48-2.1 2.07.57 4.41 1.25 5.46 2.1a1.58 1.58 0 0 1 .46 1.48 45.53 45.53 0 0 1-.78 6.87 1.49 1.49 0 0 1-.26.59 20.34 20.34 0 0 1-3.6.33 19.27 19.27 0 0 1-6.49-1.12Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M22.52 54.45c-.16-1.19-.24-2.24-.32-3.49a1.4 1.4 0 0 1 .61-1.47c1.36-.86 4.38-1.54 7.06-2.11 2.66.57 5.69 1.25 7.06 2.11a1.39 1.39 0 0 1 .6 1.47c-.08 1.25-.16 2.31-.32 3.49a29.27 29.27 0 0 1-3.72.57.29.29 0 0 0-.25.23 2.55 2.55 0 0 1-.93 1.66 3.75 3.75 0 0 1-2.43.72 3.85 3.85 0 0 1-2.46-.72 2.53 2.53 0 0 1-.92-1.66.26.26 0 0 0-.25-.23 28.89 28.89 0 0 1-3.73-.57Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M29.86 47.38c2.07.57 4.41 1.25 5.46 2.11a1.55 1.55 0 0 1 .46 1.51c-.07 1.37-.13 2.49-.28 3.81-.64.1-1.31.19-2 .25a.28.28 0 0 0-.25.23c-.25 1.72-1.73 2.4-3.36 2.4s-3.12-.67-3.39-2.4a.25.25 0 0 0-.25-.23c-.71-.07-1.39-.15-2-.25-.25-1.36-.25-2.49-.34-3.81a1.61 1.61 0 0 1 .46-1.47c1.08-.9 3.42-1.53 5.49-2.15Z"}),(0,t.createElement)(s.Path,{fill:"#0b87e8",d:"M27.55 51.94h1.2a.28.28 0 0 1 .27.28v2.36a1 1 0 0 0 .2.67.89.89 0 0 0 .65.19.85.85 0 0 0 .63-.19 1 1 0 0 0 .19-.67v-2.36a.29.29 0 0 1 .28-.28h1.2a.27.27 0 0 1 .27.28v2.5a1.91 1.91 0 0 1-.66 1.57 3 3 0 0 1-1.91.54 2.93 2.93 0 0 1-1.87-.54 1.89 1.89 0 0 1-.66-1.57v-2.5a.26.26 0 0 1 .21-.28Z"}),(0,t.createElement)(s.Path,{fill:"#57b6ff",d:"M27.55 52.08a.14.14 0 0 0-.14.14v2.5a1.72 1.72 0 0 0 .59 1.45 2.92 2.92 0 0 0 1.84.52v-1.1a1 1 0 0 1-.74-.24 1 1 0 0 1-.23-.76v-2.36a.15.15 0 0 0-.14-.14h-1.18Z"}),(0,t.createElement)(s.G,{"data-name":"Bow Tie"},(0,t.createElement)(s.Path,{fill:"#3c942c",d:"m30.5 47.19-.61 2.42c.52.87 2.58 2 3.59 2.41a.91.91 0 0 0 1.12-.1c.73-.77 1.34-2.89 1.49-4.73Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"M35.42 47.19c0 .38-.08.75-.13 1.1a7.48 7.48 0 0 1-1.16 3.18.09.09 0 0 1-.07.05 1.4 1.4 0 0 1-.34-.08 11.14 11.14 0 0 1-3.13-1.95l.57-2.3Z"}),(0,t.createElement)(s.Path,{fill:"#82e371",d:"M35 49.41a5.25 5.25 0 0 1-.9 2.06.09.09 0 0 1-.07.05 1.4 1.4 0 0 1-.34-.08 11.55 11.55 0 0 1-3-1.81h.45a5 5 0 0 0 1.64.69 8.16 8.16 0 0 1-.7-.72c1-.05 1.98-.1 2.92-.19Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"m29.14 47.19.6 2.42c-.51.87-2.58 2-3.59 2.41a.91.91 0 0 1-1.12-.1c-.73-.77-1.33-2.89-1.48-4.73Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"M24.21 47.19c0 .38.08.75.14 1.1a7.61 7.61 0 0 0 1.15 3.18.12.12 0 0 0 .07.05 1.3 1.3 0 0 0 .34-.08 11 11 0 0 0 3.14-1.95l-.58-2.3Z"}),(0,t.createElement)(s.Path,{fill:"#82e371",d:"M24.59 49.41a5.25 5.25 0 0 0 .9 2.06.12.12 0 0 0 .07.05 1.4 1.4 0 0 0 .34-.08 11.59 11.59 0 0 0 3-1.81h-.46a5.08 5.08 0 0 1-1.64.69 6 6 0 0 0 .69-.72c-.95-.05-1.93-.1-2.9-.19Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"M31.88 47.19a14.14 14.14 0 0 1-.46 3.13 2.76 2.76 0 0 1-3.22 0 14.75 14.75 0 0 1-.46-3.13Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"M28.29 47.42a14.63 14.63 0 0 0 .38 2.63 2.35 2.35 0 0 0 2.3 0 14.61 14.61 0 0 0 .37-2.63Z"}),(0,t.createElement)(s.Path,{fill:"#82e371",d:"M28.53 49.41c0 .21.08.42.14.64a2.35 2.35 0 0 0 2.3 0c.05-.22.1-.44.14-.67h-1.65Z"})),(0,t.createElement)(s.Path,{fill:"#598291",d:"m8.17 24.29 5.83-.63-2 15.08-5.51-2.68a2 2 0 0 1-1.19-1.42 15.52 15.52 0 0 1 1.32-9.46c.28-.52.59-.78 1.55-.89Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"m11.05 36.9 1.54-11.81c-.51 0-4.59.43-4.77.57 0 0-.06.11-.09.15a14.34 14.34 0 0 0-1.22 8.63A.72.72 0 0 0 7 35Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"m11.05 36.9.1-.83L7 34.37c-.31-.14-.54-1.28-.68-2.54a11.37 11.37 0 0 0 .22 2.59.74.74 0 0 0 .48.52Zm.64-5 .89-6.83c-.52 0-4.6.43-4.77.57a1 1 0 0 0-.09.15 14.61 14.61 0 0 0-1.27 4.1Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"m6.9 28 1.61 1.39a.91.91 0 0 1 .31.78c-.17 1.67-.37 3.27-.42 5.49A33.94 33.94 0 0 1 8.14 30a.16.16 0 0 0-.05-.16l-1.09-1-.14-.58A2.64 2.64 0 0 1 6.9 28Z"}),(0,t.createElement)(s.Path,{fill:"#7eaaba",d:"m6.82 28.26 1.39 1.23a.9.9 0 0 1 .31.77 32.41 32.41 0 0 0-.16 4.67c0 .23 0 .47.05.69l-.71-.34v-.35a32.31 32.31 0 0 1 .16-4.77.16.16 0 0 0-.07-.16l-1.13-1a5.3 5.3 0 0 1 .16-.74Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M33.77 5c4 3.6 4.25 8.19 2.43 13l-1.43-.55c1.59-4.22 1.49-8.16-2-11.33Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"M32.5.07a4.68 4.68 0 1 1-5.42 3.8A4.68 4.68 0 0 1 32.5.07Z"}),(0,t.createElement)(s.Path,{fill:"#3c942c",d:"M32.33 1A3.72 3.72 0 1 1 28 4a3.71 3.71 0 0 1 4.33-3Z"}),(0,t.createElement)(s.Path,{fill:"#82e371",d:"M35 2.91a3.72 3.72 0 1 1-6.82 2.86 3.72 3.72 0 0 0 6.94-1.12A4 4 0 0 0 35 2.91Zm-3.57-1.59c1.3-.06 2.19.75 2 1.8a2.81 2.81 0 0 1-2.66 2c-1.29.07-2.18-.74-2-1.79a2.82 2.82 0 0 1 2.66-2.01Z"}),(0,t.createElement)(s.Path,{fill:"#006cbf",d:"M34.58 20.64c-1.28-.23-5.52-1.11-5.67-2.78A13 13 0 0 1 30 12c.68-1.39 5-.73 6.19-.52s5.46 1.08 5.63 2.61a12.9 12.9 0 0 1-1 5.91c-.68 1.48-4.97.86-6.24.64Z"}),(0,t.createElement)(s.Path,{fill:"#006cbf",d:"M40.75 14.22c-.44-.78-3.86-1.53-4.74-1.68a22 22 0 0 0-2.8-.32 5.76 5.76 0 0 0-2.24.27 11.75 11.75 0 0 0-1 5.26c.08.38 1.65 1 2 1.11 1.81.62 5.31 1.26 7.2.87a2.05 2.05 0 0 0 .63-.23 11.88 11.88 0 0 0 .95-5.28Z"}),(0,t.createElement)(s.Path,{fill:"#57b6ff",d:"M36 12.54a21.61 21.61 0 0 0-2.8-.32 5.81 5.81 0 0 0-2.24.27 12 12 0 0 0-1 5.26c.05.14.4.38.52.46a13.67 13.67 0 0 0 4.25 1.33Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"m58.13 33.1-5.29-2.58-3.3 14.85 6.13-.62a2 2 0 0 0 1.6-.92 15.45 15.45 0 0 0 2-9.35c-.06-.59-.27-.95-1.14-1.38Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"m51.13 44 2.58-11.64c.46.22 4.18 2 4.28 2.16a1.16 1.16 0 0 1 0 .18 14.2 14.2 0 0 1-1.8 8.51.71.71 0 0 1-.62.33Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"m51.13 44 .18-.81 4.51-.19c.35 0 .94-1 1.51-2.15a10 10 0 0 1-1.1 2.35.71.71 0 0 1-.62.33Zm1.08-4.92 1.49-6.73c.45.22 4.18 2 4.28 2.16s0 .14 0 .17a14.35 14.35 0 0 1-.22 4.28Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"m58.06 37-2 .77a.84.84 0 0 0-.54.63c-.42 1.63-.78 3.19-1.49 5.3a35.12 35.12 0 0 0 2.17-5.2.17.17 0 0 1 .1-.13l1.41-.57.32-.5a1.84 1.84 0 0 0 .03-.3Z"}),(0,t.createElement)(s.Path,{fill:"#7eaaba",d:"m58 37.3-1.67.7a.86.86 0 0 0-.55.63A32 32 0 0 1 54.33 43c-.1.22-.19.42-.28.64l.78-.08c0-.1.1-.22.14-.33a33.55 33.55 0 0 0 1.48-4.54.23.23 0 0 1 .1-.14L58 38v-.7Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M29.59 48.86a72.28 72.28 0 0 1-9.24-2.24 32.78 32.78 0 0 1-7.28-3.19c-1.65-1-3.55-2.61-3.71-4.78-.48-6.33 1.29-16.33 4.06-22 1.07-2.16 4.13-2.8 6.21-3.07a40.09 40.09 0 0 1 7.43-.1 77.18 77.18 0 0 1 8.59 1.05 76.64 76.64 0 0 1 8.45 2 40.56 40.56 0 0 1 6.95 2.64c1.86 1 4.52 2.6 4.77 5 .67 6.32-1.09 16.32-3.71 22.12-.89 2-3.22 2.81-5.12 3.21a32.65 32.65 0 0 1-8 .49 71.55 71.55 0 0 1-9.4-1.13Z"}),(0,t.createElement)(s.Path,{fill:"#bdd6de",d:"M29.86 47.38c-6.32-1.11-12.5-3-16-5.22-1.81-1.14-3-2.38-3-3.63-.44-5.87 1.2-15.65 3.92-21.25.59-1.19 2.46-1.91 5.08-2.24 4-.5 9.88-.08 15.62.93s11.37 2.61 15 4.47c2.36 1.21 3.87 2.51 4 3.84.65 6.18-1.17 15.95-3.59 21.3-.52 1.15-2 1.92-4.11 2.37-4.16.88-10.61.55-16.92-.57Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M11.13 30.4a46 46 0 0 1 3.56-13.12c.59-1.19 2.46-1.91 5.08-2.24 4-.5 9.88-.08 15.62.93s11.37 2.61 15 4.47c2.36 1.21 3.87 2.51 4 3.84a46 46 0 0 1-1.15 13.55 12.61 12.61 0 0 1-2.69.49c-.37 0-.76.07-1.16.08a36 36 0 0 0 1.86-9.82c0-.62-.4-.81-1-1a69.79 69.79 0 0 0-9.39-2.82.55.55 0 0 0-.59.28A26.31 26.31 0 0 1 38 29.16c-1 1.09-8.93-.3-9.51-1.68a27.17 27.17 0 0 1-.77-4.69.53.53 0 0 0-.46-.45 68.12 68.12 0 0 0-9.78-.58c-.63 0-1.1 0-1.29.63a36.59 36.59 0 0 0-1.6 9.87l-1.05-.48a12.5 12.5 0 0 1-2.41-1.38Zm18.73 17c-6.32-1.11-12.5-3-16-5.22-1.81-1.14-3-2.38-3-3.63v-.7a3.2 3.2 0 0 0 .47.89A7.21 7.21 0 0 0 14 41.3c3.56 2.19 9.74 4.05 16.06 5.16s12.76 1.47 16.86.64a7.32 7.32 0 0 0 3.48-1.49 3.54 3.54 0 0 0 .76-.67c-.11.21-.2.43-.29.64-.51 1.15-2 1.92-4.1 2.37-4.15.88-10.6.55-16.91-.57Z"}),(0,t.createElement)(s.Path,{fill:"#bdd6de",d:"M26.18 14.88a17.72 17.72 0 0 0 1.48 2.92 26.24 26.24 0 0 0 7 2.06 26.27 26.27 0 0 0 7.34.48c.75-.6 1.67-1.5 2.4-2.22a8.06 8.06 0 0 1-2.21 3.05 26.65 26.65 0 0 1-7.66-.46 26.05 26.05 0 0 1-7.36-2.19 11.39 11.39 0 0 1-.99-3.64Z"}),(0,t.createElement)(s.Path,{fill:"#7eaaba",d:"M26.18 14.88a11.25 11.25 0 0 0 1.19 3.29 27.19 27.19 0 0 0 7.26 2.12 27.44 27.44 0 0 0 7.56.49 11.25 11.25 0 0 0 2.25-2.68l.82.26c-.6 1-1.95 2.89-2.87 3.25a27.6 27.6 0 0 1-7.9-.48 27.75 27.75 0 0 1-7.6-2.25c-.72-.66-1.34-2.94-1.58-4 .28-.02.57 0 .87 0Z"}),(0,t.createElement)(s.Path,{fill:"#1e2432",d:"M32.81 30.65c1.94.35 4.68.46 5.2-.33a36.13 36.13 0 0 0 2.26-5.47.51.51 0 0 1 .58-.36 61.87 61.87 0 0 1 10.21 3.29c.6.26 1.06.47 1 1.28a51.6 51.6 0 0 1-3.48 15.58c-.29.65-2.12 1.17-2.69 1.3a27.47 27.47 0 0 1-6.95.49 61.58 61.58 0 0 1-8.77-1 61.79 61.79 0 0 1-8.56-2.1 27.28 27.28 0 0 1-6.37-2.83c-.48-.32-2-1.43-2.07-2.14a52.39 52.39 0 0 1 2.06-15.83c.22-.77.72-.82 1.38-.85a60.07 60.07 0 0 1 10.71.4.49.49 0 0 1 .44.52 34.22 34.22 0 0 0 .24 5.96c.25.92 2.86 1.75 4.81 2.09Z"}),(0,t.createElement)(s.Path,{fill:"#2d3956",d:"M51.3 28.52A46 46 0 0 1 50 37c-.43 1.62-1.25 1.51-3 1.73-7.28.9-23.71-2-30.24-5.34-1.58-.81-2.41-1.45-2.24-2.65a62.23 62.23 0 0 1 1.68-8.45c0-.19.31-.27.48-.28a62.49 62.49 0 0 1 10.66.4.18.18 0 0 1 .14.18c-.05.6-.05 1.2-.05 1.8a38.49 38.49 0 0 0 .31 4.21c.3 1.33 3.82 2.13 5 2.33s4.8.66 5.53-.48a3 3 0 0 0 .25-.47 35.27 35.27 0 0 0 2-5.08.18.18 0 0 1 .19-.13 61 61 0 0 1 10.15 3.28c.22.1.45.29.44.47Zm-37.81 9.16v.69c0 .52 1.48 1.6 1.92 1.88 6.4 4.17 22.94 7.1 30.38 5.36.52-.11 2.24-.63 2.46-1.11.09-.2.18-.42.28-.62a9.15 9.15 0 0 1-2.18.77c-7.63 1.74-24.53-1.24-31.11-5.48a7.78 7.78 0 0 1-1.75-1.49Z"}),(0,t.createElement)(s.Path,{fill:"#82e371",d:"M20.46 29.72a14.69 14.69 0 0 0-.83 4.74c0 2.36 4.26 3.14 4.92.87a20.15 20.15 0 0 0 .84-4.75c.14-2.36-4.14-3.1-4.93-.86ZM44.73 34a15 15 0 0 1-.84 4.74c-.79 2.24-5.07 1.5-4.92-.87a20.76 20.76 0 0 1 .83-4.74c.67-2.28 4.95-1.51 4.93.87Zm-10.04 6.07c-.89 4-7 2.89-6.46-1.13a13.05 13.05 0 0 0 6.46 1.13Z"}),(0,t.createElement)(s.G,{"data-name":"Hand - Right"},(0,t.createElement)(s.Path,{fill:"#598291",d:"M12.92 47.66a5.4 5.4 0 1 1-5.39 5.39 5.4 5.4 0 0 1 5.39-5.39Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M12.92 48.56a4.49 4.49 0 1 1-4.49 4.49 4.49 4.49 0 0 1 4.49-4.49Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M12.92 48.56a4.49 4.49 0 0 1 4.49 4.49 4.69 4.69 0 0 1-.25 1.51 7.63 7.63 0 0 1-2.71.47c-2.76 0-5.09-1.37-5.84-3.24a4.5 4.5 0 0 1 4.31-3.23Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M9 44a4.3 4.3 0 0 1 1.12-2.61 4.15 4.15 0 0 1 2.4-.74c1.32 0 3.25.51 3.28 2.13.07 3.47 0 7 0 10.42a3.21 3.21 0 0 1-.43 1.8c-1.6 2.45-6.53 2.58-8.89 1.33a1.89 1.89 0 0 1-1-.91 23.4 23.4 0 0 1-.76-8.14c.1-1 .34-2.65 1.51-3A8.53 8.53 0 0 1 8.2 44Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M9.64 49.42c-.71-1.83-.75-6.7.66-7.81s5.19-.82 5.25 1.17c.07 3.48 0 6.95 0 10.43.08 3.81-6.2 4.36-9 2.9a1.63 1.63 0 0 1-.83-.75Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M10.6 49.51c-.94-1.56-.74-6.5.22-7.25 1.13-.88 3.87-.57 3.9.55.06 2.93 0 6.78 0 10.42.07 3.1-6.12 3.5-8.22 1.9Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M10 45.62c.06-1.48.34-2.84.81-3.22a3.63 3.63 0 0 1 3.2-.25 21.84 21.84 0 0 1-.05 3.26c-.17.91-2.8.76-3.96.21Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M6.32 44.58a7.38 7.38 0 0 1 1.88-.29c1.29-.06 4.74.2 5 1.89a22.26 22.26 0 0 1 0 7.33c-.32 2-5.07 2-5.82 1.12l-.86.51-.79.24a19.45 19.45 0 0 1-.71-3.8 22.15 22.15 0 0 1 0-4.23c.06-.9.27-2.47 1.3-2.77Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M6.54 45.37c2.37-.68 5.72.06 5.88 1a21.39 21.39 0 0 1 0 7c-.17 1.08-4.22 2.18-5.88 1.76-1.02-3.37-1.08-9.44 0-9.76Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M6.54 45.37a8.72 8.72 0 0 1 4.87.17 22.21 22.21 0 0 1-.07 6.81c-.14 1-3.13 1.94-5 1.92-.82-3.44-.79-8.6.2-8.9Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M5.36 49.89a15.09 15.09 0 0 1 7.92 0l-.19.74a14.32 14.32 0 0 0-7.52 0Z"})),(0,t.createElement)(s.G,{"data-name":"Hand - Left"},(0,t.createElement)(s.Path,{fill:"#598291",d:"M42.44 53.93a4.25 4.25 0 1 1-4.26 4.25 4.25 4.25 0 0 1 4.26-4.25Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M42.44 54.64a3.55 3.55 0 1 1-3.54 3.54 3.55 3.55 0 0 1 3.54-3.54Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M42.44 54.64A3.55 3.55 0 0 1 46 58.18a3.38 3.38 0 0 1-.07.72 3.47 3.47 0 0 1-1.84.52 3.55 3.55 0 0 1-3.54-3.55 3.38 3.38 0 0 1 .07-.72 3.51 3.51 0 0 1 1.82-.51Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M36.29 59.91c-.66-.1-1.3-.37-1.43-1a16.51 16.51 0 0 1-.18-3.78c0-.85 1-1.19 1.81-1.36A4.71 4.71 0 0 1 39 52.22a11.09 11.09 0 0 1 4.48.77c1.41.5 1.37 3.18 1.32 4.39 0 1-.22 3.34-1.06 4-1.51 1.28-6.84 1.39-7.21-.46a9.93 9.93 0 0 1-.24-1.01Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M39.22 54.55a12.18 12.18 0 0 1 0 4.3c-.21.77-1.38.84-2 .84s-1.91 0-2-.87a15.41 15.41 0 0 1-.19-3.71c.06-.82 1.34-1.08 2-1.18s1.97-.27 2.19.62Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M38.58 54.71c-.14-.52-2.95-.05-3 .42a15.7 15.7 0 0 0 .18 3.57c.08.44 2.67.47 2.81 0a11.62 11.62 0 0 0 .01-3.99Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M43.33 53.26c1.25.42 1.19 3.09 1.14 4.12s-.2 3.21-1 3.84a5.46 5.46 0 0 1-3 .85c-.9 0-3.48 0-3.72-1.17a22.59 22.59 0 0 1-.37-6.36c.07-.91 1.81-1.92 2.62-2.07a11 11 0 0 1 4.33.79Z"}),(0,t.createElement)(s.Path,{fill:"#d3e8ef",d:"M43.09 53.86a10.19 10.19 0 0 0-4-.73A3.42 3.42 0 0 0 37 54.61a22 22 0 0 0 .36 6.18c.16.75 4.47 1 5.71-.06.82-.67 1.21-6.54.02-6.87Z"}),(0,t.createElement)(s.Path,{fill:"#fff",d:"M43.09 53.86a10.92 10.92 0 0 0-3.88-.75v7.15a9.13 9.13 0 0 0 4.29-.44c.5-1.82.5-5.69-.41-5.96Zm-5.77.31a1.07 1.07 0 0 0-.29.42 22 22 0 0 0 .36 6.19c.16.75 4.47 1 5.71-.06a.86.86 0 0 0 .2-.27c-1.33 1-5.51.75-5.66 0a22.28 22.28 0 0 1-.35-6.17.21.21 0 0 1 .03-.11Z"}),(0,t.createElement)(s.Path,{fill:"#598291",d:"M36.6 57.26a8.58 8.58 0 0 1 2.93-.62c.21 0 .18.7 0 .7a8.1 8.1 0 0 0-2.75.58Z"}),(0,t.createElement)(s.Path,{fill:"#7eaaba",d:"M43.5 59.82c-.34-.14-.69-.3-1.07-.48a.24.24 0 0 1-.12-.22 17.92 17.92 0 0 0 0-4 .21.21 0 0 1 .13-.22c.38-.18.75-.34 1.11-.47a.49.49 0 0 1 .12.41c-.34.12-.66.26-1 .39a17.78 17.78 0 0 1 0 3.79l.9.4a.54.54 0 0 1-.07.4Z"})))),m=({title:e})=>(0,t.createElement)("div",{className:"uap-magic-header"},(0,t.createElement)(s.Icon,{className:"uap-magic-header__icon",icon:n}),(0,t.createElement)("div",{className:"uap-magic-header__text"},e)),d=({uniqueID:e,trigger:a,triggers:c,isSidebar:r=!1,setAttributes:i,label:n="",help:m=""})=>{const[d,g]=(0,l.useState)([]),[o,f]=(0,l.useState)(!0),[u,h]=(0,l.useState)(0),E=automatorProBlocks["magic-triggers"];return(0,l.useEffect)((()=>{let e=[];if(null===c)return e.push({label:E.loading,value:0}),void g(e);if(0===c.length)return e.push({label:a.translations.not_found,value:0}),void g(e);e.push({label:a.translations.select_label,value:0});const t=c.map((({id:e,option:t})=>({label:t,value:parseInt(e)})));e=[...e,...t];const l=a&&a.id?parseInt(a.id):0;h(l),f(!1),g(e)}),[c,a]),(0,t.createElement)((({condition:e,wrapper:t,children:a})=>e?t(a):a),{condition:!r,wrapper:e=>(0,t.createElement)("div",{className:"uap-magic-trigger-editor-select"},e)},(0,t.createElement)(s.SelectControl,{key:e,label:n,help:m,value:u,options:d,disabled:o,onChange:e=>{i({id:parseInt(e)})}}))},g=()=>(0,t.createElement)(s.Icon,{icon:()=>(0,t.createElement)("svg",{width:"15",height:"15",viewBox:"0 0 15 15",fill:"none",xmlns:"http://www.w3.org/2000/svg"},(0,t.createElement)("path",{d:"M7.49627 7.20586C7.88446 7.20586 8.1994 7.52051 8.1994 7.90898C8.166 10.0257 8.43787 12.0662 9.01092 14.1067C9.0909 14.3915 8.94793 15 8.33358 15C8.02655 15 7.74442 14.7973 7.65711 14.4867C7.26072 13.0831 6.74774 10.8126 6.79344 7.90898C6.79315 7.5208 7.1078 7.20586 7.49627 7.20586ZM7.52147 4.81142C9.31619 4.80937 10.5686 6.19043 10.5437 7.77304C10.5218 9.17754 10.6536 10.5829 10.9354 11.9493C11.0142 12.3296 10.7693 12.702 10.3893 12.7802C10.007 12.8584 9.63729 12.6144 9.55848 12.2341C9.25555 10.7666 9.11405 9.2581 9.13748 7.75107C9.14949 6.99082 8.55389 6.21328 7.54227 6.21767C6.62498 6.23144 5.86795 6.96006 5.85418 7.84277C5.83162 9.25049 5.93651 10.6649 6.16502 12.0483C6.2286 12.431 5.96903 12.7931 5.58612 12.8563C5.00106 12.9539 4.80067 12.414 4.77811 12.2771C4.53553 10.8114 4.42479 9.31201 4.44793 7.8208C4.47371 6.18603 5.85242 4.83603 7.52147 4.81142ZM10.7643 4.23193C11.0661 3.9876 11.5076 4.03271 11.7534 4.33388C12.504 5.25674 12.9074 6.42217 12.8887 7.61513C12.8714 8.71611 12.961 9.82031 13.1555 10.8978C13.2241 11.2802 12.9707 11.6458 12.5884 11.7149C11.9992 11.8175 11.7953 11.2811 11.7713 11.1478C11.5606 9.98056 11.4637 8.78467 11.4824 7.59316C11.4962 6.71777 11.2129 5.89746 10.6621 5.22099C10.4175 4.91982 10.4632 4.47715 10.7643 4.23193ZM7.55721 2.40586C8.00867 2.39385 8.4616 2.44804 8.89842 2.55146C9.27606 2.64111 9.50985 3.01963 9.42078 3.39785C9.33114 3.77549 8.95203 4.0081 8.5744 3.92021C8.25067 3.84375 7.9161 3.80976 7.5783 3.81211C5.36844 3.84521 3.54324 5.61387 3.50955 7.75488C3.49227 8.86465 3.54705 9.98467 3.67303 11.0839C3.71698 11.4697 3.44012 11.8186 3.05457 11.8626C2.56473 11.9197 2.30721 11.5145 2.27586 11.2441C2.14274 10.0787 2.0851 8.90581 2.1033 7.73291C2.14901 4.83984 4.59559 2.4498 7.55721 2.40586ZM0.172347 5.96396C0.256136 5.58545 0.626741 5.34287 1.01053 5.42871C1.38963 5.5125 1.62928 5.8875 1.54549 6.26689C1.4075 6.89414 1.40164 7.36289 1.4078 8.07158C1.41102 8.46035 1.09871 8.77734 0.71053 8.78056H0.704671C0.318831 8.78056 0.00476844 8.46972 0.00154579 8.0833C-0.00372765 7.43349 -0.0101725 6.79101 0.172347 5.96396ZM1.34774 3.30615C2.7745 1.27324 5.10916 0.037791 7.59324 0.000584007C9.63787 -0.0240254 11.529 0.729783 12.9355 2.13691C14.294 3.49717 15.0262 5.3039 14.9957 7.22607L14.9992 7.85508C15.0106 8.24326 14.7054 8.56728 14.3172 8.57842C14.3104 8.579 14.3031 8.579 14.2961 8.579C13.9176 8.579 13.605 8.27724 13.5938 7.89638L13.5891 7.20381C13.6135 5.66572 13.0278 4.21963 11.9403 3.13154C10.8048 1.99482 9.26434 1.3834 7.61375 1.40713C5.57908 1.4373 3.66658 2.44892 2.49793 4.11387C2.27498 4.43203 1.837 4.50791 1.51883 4.28554C1.20125 4.0623 1.1245 3.62373 1.34774 3.30615Z",fill:"black",fillOpacity:"0.6"}))}),o=({trigger:e={},isLoading:a=!0})=>{const{id:l="",title:c="",recipeID:r=""}=e,i=automatorProBlocks["magic-triggers"],s=(e,t)=>a?void 0:i[`tooltip_${e}_${t}`],n=({postType:n})=>{const m=(t=>a?void 0:"publish"===e[t]?"live":"draft")("trigger"===n?"triggerStatus":"recipeStatus"),d=`${i.trigger_label} ID: ${l}`,g=`${i.recipe_label}:  ${a?i.loading:`${c} ( ID: ${r} )`}`;return(0,t.createElement)("span",{className:"uap-magic-trigger-post"},"trigger"===n?d:g,(0,t.createElement)("span",{className:"uap-magic-trigger-post__status","data-status":m,title:s(n,m)}))};return(0,t.createElement)("div",{className:"uap-magic-trigger-selected__content"},(0,t.createElement)("div",{className:"uap-magic-trigger-selected__info"},(0,t.createElement)("span",{className:"uap-magic-trigger-selected__info-icon"},(0,t.createElement)(g,null)),(0,t.createElement)("span",{className:"uap-magic-trigger-selected__info-text"},(0,t.createElement)(n,{postType:"trigger"}),(0,t.createElement)(n,{postType:"recipe"}))))},f=({trigger:e})=>{const{is_ajax:a,triggerType:c,label:r,submit_message:i,success_message:n,translations:m}=e,[d,g]=(0,l.useState)(""!==r?r:m.label),[o,f]=(0,l.useState)(!1),u="button"===c?"secondary":"link",h="button"===c?"automator_button":"automator_link";return(0,l.useEffect)((()=>{g(""!==r?r:m.label)}),[r,m]),(0,t.createElement)("div",{className:"uap-magic-trigger-element"},(0,t.createElement)(s.Button,{className:h,variant:u,onClick:e=>{e.preventDefault(),"no"===a||o||(f(!0),g(""!==i?i:m.submit_message),setTimeout((()=>{g(""!==n?n:m.success_message),setTimeout((()=>{g(r),f(!1)}),2e3)}),2e3))}},d))},u=({editState:e,setAttributes:a,translations:c})=>{const{trigger:r,triggers:n}=e,m=r&&r.label?r.label:"",g=r&&"yes"===r.is_ajax,o=r&&r.submit_message?r.submit_message:"",f=r&&r.success_message?r.success_message:"",u="selected"===e.status,h=automatorProBlocks["magic-triggers"],E=(e,t)=>{""===e.target.value&&a({[t]:c[t]})};return u?(0,t.createElement)(l.Fragment,null,(0,t.createElement)(i.InspectorControls,null,(0,t.createElement)(s.PanelBody,{title:h.sidebar_trigger_label},(0,t.createElement)(d,{uniqueID:"uap-editor-sidebar-triger-select",label:h.recipe_label,trigger:r,setAttributes:a,triggers:n,isSidebar:!0})),(0,t.createElement)(s.PanelBody,{title:c.panel_label},(0,t.createElement)(s.TextControl,{label:c.input_label,value:m,onChange:e=>a({label:e}),onBlur:e=>E(e,"label"),placeholder:c.label}),(0,t.createElement)(s.ToggleControl,{label:h.sidebar_ajax_label,checked:g,onChange:e=>a({is_ajax:e?"yes":"no"}),help:h.sidebar_ajax_help}),g&&(0,t.createElement)(l.Fragment,null,(0,t.createElement)(s.TextControl,{label:h.sidebar_submit_message_label,value:o,onChange:e=>a({submit_message:e}),onBlur:e=>E(e,"submit_message"),help:h.sidebar_submit_message_help,placeholder:c.submit_message}),(0,t.createElement)(s.TextControl,{label:h.sidebar_success_message_label,value:f,onChange:e=>a({success_message:e}),onBlur:e=>E(e,"success_message"),help:h.sidebar_success_message_help,placeholder:c.success_message}))))):null};function h({attributes:e,setAttributes:a,blockTitle:c}){const s=automatorProBlocks["magic-button"],n=((e,t,a)=>{const c={id:e.id||0,recipeID:"",title:"",triggerStatus:"",recipeStatus:"",editor_help:t.editor_help,triggerType:a.endsWith("Button")?"button":"link",label:e.label||t.label,is_ajax:e.is_ajax,submit_message:e.submit_message||t.submit_message,success_message:e.success_message||t.success_message,translations:t},[r,i]=(0,l.useState)(c);return(0,l.useEffect)((()=>{i(c)}),[e,t,a]),r})(e,s,c),{triggers:g,isLoading:h}=(e=>{const[t,a]=(0,l.useState)(null),[c,i]=(0,l.useState)(!0);return(0,l.useEffect)((()=>{r()({path:`/uap/blocks/v1/magic-triggers?type=${e}`}).then((e=>{a(e),i(!1)})).catch((e=>{console.error(`Error fetching magic triggers data: ${e}`)}))}),[e]),{triggers:t,isLoading:c}})("button"),E=((e,t,a,c)=>{const[r,i]=(0,l.useState)({status:"loading",triggers:null,trigger:c}),s=(0,l.useRef)(r),n=(0,l.useMemo)((()=>{if(!t)return s.current;let l=null;const r={status:"select",triggers:t,trigger:{...c,id:parseInt(a.id)}};return!e&&a.id&&(l=t.find((e=>parseInt(e.id)===r.trigger.id))),l&&(r.status="selected",r.trigger={...r.trigger,recipeID:parseInt(l.recipeID),title:l.title,triggerStatus:l.triggerStatus,recipeStatus:l.recipeStatus}),r}),[e,t,a,c]);return(0,l.useEffect)((()=>{e||n===s.current||(i(n),s.current=n)}),[e,n]),r})(h,g,e,n),p=((e,t,a)=>((e,t,a,l)=>{const c=((e,t,a)=>e?t?"selected":"select":"selected"===a?"selected":"select")(t,a,l);return e["data-uap-magic-display"]=c,e})((0,i.useBlockProps)(),e,t,a))(h,e.id,E.status),b=((e,a,c,r)=>(0,l.useMemo)((()=>"selected"===e?(0,t.createElement)(l.Fragment,null,(0,t.createElement)(f,{trigger:c.trigger}),(0,t.createElement)(o,{trigger:c.trigger,isLoading:a})):(0,t.createElement)(d,{uniqueID:"uap-editor-triger-select",trigger:c.trigger,setAttributes:r,triggers:c.triggers,help:c.trigger.editor_help})),[e,c.status,c.trigger,c.triggers,r]))(p["data-uap-magic-display"],h,E,a);return(0,t.createElement)("div",{...p},(0,t.createElement)("div",{className:"uap-magic-wrapper"},(0,t.createElement)(m,{title:c}),b,(0,t.createElement)(u,{editState:E,setAttributes:a,translations:s})))}const E=JSON.parse('{"UU":"uncanny-automator/magic-button","DD":"Magic Button"}');(0,a.registerBlockType)(E.UU,{edit:e=>(0,t.createElement)(h,{...e,blockTitle:E.DD}),icon:n})})();