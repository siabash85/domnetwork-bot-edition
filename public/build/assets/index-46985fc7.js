import{d as A,r,o as F,c as n,l as p,g as a,w as e,h as x,A as k,f as _,i as t,j as o,F as M,m as U,t as E,e as b}from"./app-fa5d0039.js";import{a as I,B as L}from"./index-db91f07a.js";const T={class:"grid grid-cols-12 gap-4"},q={class:"col-span-12 lg:col-span-12"},z={class:"flex justify-between mb-12 items-center"},G=t("h2",{class:"text-xl"},"لیست پکیج ها",-1),H=t("thead",null,[t("tr",null,[t("th",{class:"text-right"},"نام"),t("th",{class:"text-right"},"وضعیت"),t("th",{class:"text-right"},"عملیات")])],-1),J={class:"whitespace-nowrap"},K={class:"whitespace-nowrap"},O={class:"flex items-center"},X=A({__name:"index",setup(P){const d=r(!1),u=r(!1),v=r(!0),m=r([]),f=r(null),h=async()=>{const{data:s}=await k.get("/api/panel/packages");m.value=s.data,v.value=!1},w=s=>{d.value=!0,f.value=s},V=async()=>{const{data:s}=await k.delete(`/api/panel/packages/${f.value.id}`);s.status==200&&(d.value=!1,h(),u.value=!0)};return F(()=>{h()}),(s,c)=>{const i=n("v-btn"),g=n("v-chip"),y=n("v-table"),B=n("v-card-title"),C=n("v-card-text"),S=n("v-spacer"),D=n("v-card-actions"),N=n("v-card"),$=n("v-dialog"),j=n("v-snackbar");return _(),p("div",null,[a(x(L),{animated:"",loading:v.value},{template:e(()=>[t("div",T,[t("div",q,[a(x(I),{variant:"card",class:"h-[300px]"})])])]),default:e(()=>[t("div",z,[G,a(i,{to:{name:"panel-packages-create"},color:"blue-accent-2"},{default:e(()=>[o(" ایجاد پکیج ")]),_:1})]),a(y,{"fixed-header":"",height:"700px"},{default:e(()=>[H,t("tbody",null,[(_(!0),p(M,null,U(m.value,l=>(_(),p("tr",{key:l.name},[t("td",null,[t("div",J,E(l.name),1)]),t("td",null,[t("div",K,[l.is_active?(_(),b(g,{key:0,color:"green","text-color":"white"},{default:e(()=>[o(" فعال ")]),_:1})):(_(),b(g,{key:1,color:"red","text-color":"white"},{default:e(()=>[o(" غیرفعال ")]),_:1}))])]),t("td",null,[t("div",O,[a(i,{to:{name:"panel-packages-edit",params:{id:l.id}},"prepend-icon":"mdi-pencil-box-outline"},{default:e(()=>[o(" ویرایش ")]),_:2},1032,["to"]),a(i,{onClick:Q=>w(l),"prepend-icon":"mdi-trash-can-outline",class:"mr-4"},{default:e(()=>[o(" حذف ")]),_:2},1032,["onClick"])])])]))),128))])]),_:1})]),_:1},8,["loading"]),a($,{modelValue:d.value,"onUpdate:modelValue":c[1]||(c[1]=l=>d.value=l),persistent:"",width:"350px"},{default:e(()=>[a(N,null,{default:e(()=>[a(B,{class:""},{default:e(()=>[o(" آیا از حذف این آیتم اطمینان دارید؟ ")]),_:1}),a(C,null,{default:e(()=>[o("آیا از حذف این آیتم اطمینان دارید؟")]),_:1}),a(D,null,{default:e(()=>[a(S),a(i,{color:"green-darken-1",variant:"text",onClick:c[0]||(c[0]=l=>d.value=!1)},{default:e(()=>[o(" نه ")]),_:1}),a(i,{color:"green-darken-1",onClick:V},{default:e(()=>[o(" بله ")]),_:1})]),_:1})]),_:1})]),_:1},8,["modelValue"]),a(j,{modelValue:u.value,"onUpdate:modelValue":c[2]||(c[2]=l=>u.value=l),timeout:2e3},{default:e(()=>[o(" پکیج با موفقیت حذف شد. ")]),_:1},8,["modelValue"])])}}});export{X as default};
