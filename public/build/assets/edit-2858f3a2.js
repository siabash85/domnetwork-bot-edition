import{d as U,r as d,u as R,n as B,o as C,c as n,l as N,g as a,w as l,A as f,f as A,k as D,j as b,i as r}from"./app-5f0513f2.js";const M=r("div",{class:"mb-6"},[r("h2",{class:"text-xl"},"ویرایش سرور")],-1),$=r("div",null,"وضعیت",-1),j=r("div",null,"سرور پیش فرض",-1),T=U({__name:"edit",setup(E){const _=d(!1),m=d(null),e=d({name:null,is_active:!1,is_default:!1}),v=d(!1),V=d([o=>o?!0:"نام  سرور  الزامی می باشد"]),g=R(),c=B(),x=async o=>{const{valid:t}=await m.value.validate();if(t){_.value=!0;const u=new FormData;u.append("name",e.value.name),u.append("is_active",e.value.is_active),u.append("is_default",e.value.is_default);const{data:i}=await f.put(`/api/panel/servers/${c.params.id}`,u);i.status==200&&(v.value=!0,g.push({name:"panel-servers-index"}))}},k=async()=>{const{data:o}=await f.get(`/api/panel/servers/${c.params.id}`);e.value.name=o.data.name,e.value.is_active=o.data.is_active.toString(),e.value.is_default=o.data.is_default.toString()};return C(()=>{k()}),(o,t)=>{const u=n("v-text-field"),i=n("v-radio"),p=n("v-radio-group"),w=n("v-btn"),h=n("v-form"),y=n("v-sheet"),S=n("v-snackbar");return A(),N("div",null,[a(y,null,{default:l(()=>[M,a(h,{ref_key:"formRef",ref:m,"validate-on":"submit",onSubmit:D(x,["prevent"])},{default:l(()=>[a(u,{modelValue:e.value.name,"onUpdate:modelValue":t[0]||(t[0]=s=>e.value.name=s),rules:V.value,label:"نام",density:"compact","single-line":"",variant:"solo"},null,8,["modelValue","rules"]),a(p,{modelValue:e.value.is_active,"onUpdate:modelValue":t[1]||(t[1]=s=>e.value.is_active=s)},{label:l(()=>[$]),default:l(()=>[a(i,{label:"فعال",value:"1"}),a(i,{label:"غیرفعال",value:"0"})]),_:1},8,["modelValue"]),a(p,{modelValue:e.value.is_default,"onUpdate:modelValue":t[2]||(t[2]=s=>e.value.is_default=s)},{label:l(()=>[j]),default:l(()=>[a(i,{label:"می باشد",value:"1"}),a(i,{label:"نمی باشد",value:"0"})]),_:1},8,["modelValue"]),a(w,{loading:_.value,color:"light-blue-accent-4",type:"submit",block:"",class:"mt-2"},{default:l(()=>[b("ویرایش")]),_:1},8,["loading"])]),_:1},8,["onSubmit"])]),_:1}),a(S,{absolute:"",modelValue:v.value,"onUpdate:modelValue":t[3]||(t[3]=s=>v.value=s),timeout:2e4},{default:l(()=>[b(" سرور با موفقیت ویرایش شد. ")]),_:1},8,["modelValue"])])}}});export{T as default};