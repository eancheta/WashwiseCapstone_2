import{M as n}from"./app-BjUKoKCR.js";/**
 * @license lucide-vue-next v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const u=e=>e.replace(/([a-z0-9])([A-Z])/g,"$1-$2").toLowerCase(),p=e=>e.replace(/^([A-Z])|[\s-_]+(\w)/g,(t,o,r)=>r?r.toUpperCase():o.toLowerCase()),C=e=>{const t=p(e);return t.charAt(0).toUpperCase()+t.slice(1)},g=(...e)=>e.filter((t,o,r)=>!!t&&t.trim()!==""&&r.indexOf(t)===o).join(" ").trim(),w=e=>e==="";/**
 * @license lucide-vue-next v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */var c={xmlns:"http://www.w3.org/2000/svg",width:24,height:24,viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":2,"stroke-linecap":"round","stroke-linejoin":"round"};/**
 * @license lucide-vue-next v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const k=({name:e,iconNode:t,absoluteStrokeWidth:o,"absolute-stroke-width":r,strokeWidth:s,"stroke-width":a,size:i=c.width,color:h=c.stroke,...l},{slots:d})=>n("svg",{...c,...l,width:i,height:i,stroke:h,"stroke-width":w(o)||w(r)||o===!0||r===!0?Number(s||a||c["stroke-width"])*24/Number(i):s||a||c["stroke-width"],class:g("lucide",l.class,...e?[`lucide-${u(C(e))}-icon`,`lucide-${u(e)}`]:["lucide-icon"])},[...t.map(m=>n(...m)),...d.default?[d.default()]:[]]);/**
 * @license lucide-vue-next v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const f=(e,t)=>(o,{slots:r,attrs:s})=>n(k,{...s,...o,iconNode:t,name:e},r);/**
 * @license lucide-vue-next v0.544.0 - ISC
 *
 * This source code is licensed under the ISC license.
 * See the LICENSE file in the root directory of this source tree.
 */const v=f("loader-circle",[["path",{d:"M21 12a9 9 0 1 1-6.219-8.56",key:"13zald"}]]);export{v as L};
