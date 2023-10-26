import{d as F,r as i,o as M,c as o,l as p,g as t,w as e,h as x,A as k,f as u,i as a,j as n,F as U,m as E,t as I,e as b,n as w}from"./app-fa5d0039.js";import{a as L,B as T}from"./index-db91f07a.js";const q={class:"grid grid-cols-12 gap-4"},z={class:"col-span-12 lg:col-span-12"},G={class:"flex justify-between mb-12 items-center"},H=a("h2",{class:"text-xl"},"لیست پلتفرم ها",-1),J=a("thead",null,[a("tr",null,[a("th",{class:"text-right"},"نام"),a("th",{class:"text-right"},"وضعیت"),a("th",{class:"text-right"},"عملیات")])],-1),K={class:"whitespace-nowrap"},O={class:"whitespace-nowrap"},P={class:"flex items-center"},Y=F({__name:"index",setup(Q){const r=i(!1),_=i(!1),v=i(!0),m=i([]),f=i(null),h=async()=>{const{data:s}=await k.get("/api/panel/guide/platforms");m.value=s.data,v.value=!1},V=s=>{r.value=!0,f.value=s},y=async()=>{const{data:s}=await k.delete(`/api/panel/guide/platforms/${f.value.id}`);s.status==200&&(r.value=!1,h(),_.value=!0)};return M(()=>{h()}),(s,c)=>{const d=o("v-btn"),g=o("v-chip"),C=o("v-table"),B=o("v-card-title"),S=o("v-card-text"),D=o("v-spacer"),N=o("v-card-actions"),$=o("v-card"),j=o("v-dialog"),A=o("v-snackbar");return u(),p("div",null,[t(x(T),{animated:"",loading:v.value},{template:e(()=>[a("div",q,[a("div",z,[t(x(L),{variant:"card",class:"h-[300px]"})])])]),default:e(()=>[a("div",G,[H,t(d,{to:{name:"panel-platforms-create"},color:"blue-accent-2"},{default:e(()=>[n(" ایجاد پلتفرم ")]),_:1})]),t(C,{"fixed-header":"",height:"700px"},{default:e(()=>[J,a("tbody",null,[(u(!0),p(U,null,E(m.value,l=>(u(),p("tr",{key:l.name},[a("td",null,[a("div",K,I(l==null?void 0:l.name),1)]),a("td",null,[a("div",O,[l.status=="active"?(u(),b(g,{key:0,color:"green","text-color":"white"},{default:e(()=>[n(" فعال ")]),_:1})):w("",!0),l.status=="purchased"?(u(),b(g,{key:1,color:"warning","text-color":"white"},{default:e(()=>[n(" خریداری شده ")]),_:1})):w("",!0)])]),a("td",null,[a("div",P,[t(d,{to:{name:"panel-platforms-clients-index",params:{id:l.id}},"prepend-icon":"mdi-pencil-box-outline"},{default:e(()=>[n(" برنامه ها ")]),_:2},1032,["to"]),t(d,{to:{name:"panel-platforms-edit",params:{id:l.id}},"prepend-icon":"mdi-pencil-box-outline",class:"mr-4"},{default:e(()=>[n(" ویرایش ")]),_:2},1032,["to"]),t(d,{onClick:R=>V(l),"prepend-icon":"mdi-trash-can-outline",class:"mr-4"},{default:e(()=>[n(" حذف ")]),_:2},1032,["onClick"])])])]))),128))])]),_:1})]),_:1},8,["loading"]),t(j,{modelValue:r.value,"onUpdate:modelValue":c[1]||(c[1]=l=>r.value=l),persistent:"",width:"350px"},{default:e(()=>[t($,null,{default:e(()=>[t(B,{class:""},{default:e(()=>[n(" آیا از حذف این آیتم اطمینان دارید؟ ")]),_:1}),t(S,null,{default:e(()=>[n("آیا از حذف این آیتم اطمینان دارید؟")]),_:1}),t(N,null,{default:e(()=>[t(D),t(d,{color:"green-darken-1",variant:"text",onClick:c[0]||(c[0]=l=>r.value=!1)},{default:e(()=>[n(" نه ")]),_:1}),t(d,{color:"green-darken-1",onClick:y},{default:e(()=>[n(" بله ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),t(A,{modelValue:_.value,"onUpdate:modelValue":c[2]||(c[2]=l=>_.value=l),timeout:2e3},{default:e(()=>[n(" پلتفرم با موفقیت حذف شد. ")]),_:1},8,["modelValue"])])}}});export{Y as default};
