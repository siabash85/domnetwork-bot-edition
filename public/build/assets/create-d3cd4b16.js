import{d as B,r as u,u as E,n as N,o as A,c as d,l as D,g as e,w as i,f as M,h as n,q as S,i as s,v as r,x as g,E as m,j as V,A as j}from"./app-585073a5.js";const P=s("div",{class:"mb-6"},[s("h2",{class:"text-xl"},"ایجاد برنامه")],-1),T={class:"grid grid-cols-12 gap-4"},$={class:"col-span-4"},G={class:"invalid-feedback d-block"},H={class:"col-span-4"},I={class:"invalid-feedback d-block"},J={class:"col-span-4"},K={class:"col-span-12"},L={class:"invalid-feedback d-block"},O={class:"col-span-12"},Q={class:"invalid-feedback d-block"},Y=B({__name:"create",setup(W){const k=u(!1),p=u(null),a=u({name:null,link:null,description:null,status:"active",video:null}),_=u(!1);u([c=>c?!0:"نام   برنامه  الزامی می باشد"]);const h=u([{state:"فعال",value:"active"},{state:"غیرفعال",value:"inactive"}]),x=E(),f=N(),C=async c=>{const{valid:l}=await p.value.validate();if(l){k.value=!0;const t=new FormData;t.append("guide_platform_id",f.params.id),t.append("name",a.value.name),t.append("link",a.value.link),t.append("status",a.value.status),t.append("description",a.value.description),a.value.video&&t.append("video",a.value.video);const{data:b}=await j.post(`/api/panel/guide/platform/${f.params.id}/clients`,t);b.status==200&&(_.value=!0,x.push({name:"panel-platforms-clients-index",params:{id:f.params.id}}))}},q=c=>{a.value.video=c.target.files[0],p.value.setFieldValue("video",a.value.video)},w=async()=>{};return A(()=>{w()}),(c,l)=>{const t=d("v-text-field"),b=d("v-select"),U=d("v-textarea"),y=d("v-file-input"),z=d("v-btn"),F=d("v-sheet"),R=d("v-snackbar");return M(),D("div",null,[e(F,null,{default:i(()=>[P,e(n(S),{ref_key:"formRef",ref:p,onSubmit:C},{default:i(()=>[s("div",T,[s("div",$,[e(n(r),{mode:"passive",name:"name",rules:"required",label:" عنوان"},{default:i(({field:o})=>[e(t,g({modelValue:a.value.name,"onUpdate:modelValue":l[0]||(l[0]=v=>a.value.name=v),label:"عنوان","single-line":"",variant:"solo-filled",size:"large"},o,{"hide-details":"auto"}),null,16,["modelValue"])]),_:1}),s("div",G,[e(n(m),{name:"name"})])]),s("div",H,[e(n(r),{mode:"passive",name:"link",rules:"required",label:" لینک دانلود برنامه"},{default:i(({field:o})=>[e(t,g({modelValue:a.value.link,"onUpdate:modelValue":l[1]||(l[1]=v=>a.value.link=v),label:"لینک دانلود برنامه","single-line":"",variant:"solo-filled",size:"large"},o,{"hide-details":"auto"}),null,16,["modelValue"])]),_:1}),s("div",I,[e(n(m),{name:"link"})])]),s("div",J,[e(b,{modelValue:a.value.status,"onUpdate:modelValue":l[2]||(l[2]=o=>a.value.status=o),label:"انتخاب  وضعیت",items:h.value,"item-title":"state","item-value":"value","single-line":"",variant:"solo-filled"},null,8,["modelValue","items"])]),s("div",K,[e(n(r),{mode:"passive",name:"description",rules:"required",label:"توضیحات"},{default:i(({field:o})=>[e(U,g({modelValue:a.value.description,"onUpdate:modelValue":l[3]||(l[3]=v=>a.value.description=v),label:"توضیحات","single-line":"",variant:"solo-filled",size:"large"},o,{"hide-details":"auto"}),null,16,["modelValue"])]),_:1}),s("div",L,[e(n(m),{name:"description"})])]),s("div",O,[e(n(r),{mode:"passive",name:"video",rules:"required",label:"ویدیو آموزشی"},{default:i(({field:o})=>[e(y,{accept:"video/*",label:"ویدیو آموزشی",size:"large","hide-details":"auto",onChange:q})]),_:1}),s("div",Q,[e(n(m),{name:"video"})])])]),e(z,{loading:k.value,color:"light-blue-accent-4",type:"submit",block:"",class:"mt-2"},{default:i(()=>[V("ایجاد")]),_:1},8,["loading"])]),_:1},512)]),_:1}),e(R,{absolute:"",modelValue:_.value,"onUpdate:modelValue":l[4]||(l[4]=o=>_.value=o),timeout:2e4},{default:i(()=>[V(" برنامه با موفقیت ایجاد شد. ")]),_:1},8,["modelValue"])])}}});export{Y as default};
