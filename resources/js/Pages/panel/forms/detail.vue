<template>
    <div>
        <section class="mb-4">
            <div class="flex justify-end">
                <v-btn
                    :loading="loader"
                    color="light-blue-accent-4"
                    type="submit"
                    class="mt-2"
                    @click="convertToPdf"
                    >دانلود فایل
                </v-btn>
            </div>
        </section>
        <div class="form-detail-module mx-auto p-6" ref="formRef">
            <div class="grid grid-cols-12 gap-x-8 mb-2">
                <div class="col-span-6">
                    <div class="flex items-center pl-8 mb-2">
                        <div class="max-w-[230px]">
                            <div class="text-[28px] leading-7 font-semibold">
                                باشگاه تناسب اندام و بدنسازی میلان
                            </div>
                        </div>
                        <div class="mr-3 -mb-2">
                            <img
                                class="form-detail-logo"
                                src="/panel/media/logo.jpg"
                                alt=""
                            />
                        </div>
                    </div>
                    <div
                        class="border-b-2 pb-2 mt-1 pt-1 border-gray-600"
                    ></div>
                </div>
                <div class="col-span-6">
                    <ul>
                        <li class="text-sm text-gray-800">
                            1. حین تمرین از مچ بند،کمربند،لوازم ایمنی تمرین
                            استفاده کنید
                        </li>
                        <li class="text-sm text-gray-800">
                            2. حین تمرین جرعه جرعه آب بنوشید
                        </li>
                        <li class="text-sm text-gray-800 whitespace-nowrap">
                            3. گرم و سرد کردن بدن قبل و بعد از تمرین 8 دقیقه
                            الزامی است.
                        </li>
                        <li class="text-sm text-gray-800">
                            4. جهت گرفتن برنامه بعدی این برنامه را به همراه
                            داشته باشید.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="form-detail-module__header mb-1">
                <div class="flex items-center">
                    <div class="flex items-center grow">
                        <div class="text-gray-500 text-sm">
                            نام و نام خانوادگی :
                        </div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.first_name }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-sm">قد:</div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.height }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-sm">وزن:</div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.weight }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-sm">سن:</div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.age }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-sm">سابقه ورزشی :</div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.sports_history }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-sm">تاریخ:</div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.modified_date }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center mt-2">
                    <div class="flex items-center grow">
                        <div class="text-gray-500 text-xs">
                            نوع آسیب یا بیماری :
                        </div>
                        <div class="mr-1 text-gray-700 font-bold text-xs">
                            {{ form?.illness }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-xs">هدف تمرین:</div>
                        <div class="mr-1 text-gray-700 font-bold text-xs">
                            {{ form?.purpose }}
                        </div>
                    </div>
                    <div class="flex items-center mr-2 grow">
                        <div class="text-gray-500 text-sm">
                            مدت زمان استفاده از برنامه :
                        </div>
                        <div class="mr-1 text-gray-700 font-bold text-sm">
                            {{ form?.use_duration }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center pb-1">
                <div>با مدیریت و مربیگری رسمی فدراسیون‌: کیان دهقانی نیا</div>
                <div class="mr-6 flex items-center grow">
                    <div class="ml-2">gymmilan</div>
                    <div>
                        <i
                            class="mdi-instagram mdi v-icon notranslate v-theme--light v-icon--size-default"
                            aria-hidden="true"
                            density="compact"
                        ></i>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center">
                        <div>بانوان:</div>
                        <div class="mr-1">38392403</div>
                    </div>
                    <div class="flex items-center mr-4">
                        <div>آقایان:</div>
                        <div class="mr-1">38392404</div>
                    </div>
                </div>
            </div>
            <div
                class="form-detail-module__items border-2 border-gray-900 grid grid-cols-12 mt-3"
            >
                <div
                    class="border-2 border-gray-900 h-[430px] col-span-6"
                    v-for="(program, index) in form_programs"
                    :key="index"
                >
                    <div class="flex justify-start font-bold pr-2 pb-0 text-xl">
                        <template v-if="program.class == '1'">A</template>
                        <template v-if="program.class == '2'">B</template>
                        <template v-if="program.class == '3'">C</template>
                        <template v-if="program.class == '4'">D</template>
                    </div>

                    <div class="px-3">
                        <div
                            class="border-b py-[2px] border-gray-800"
                            v-for="(movement, index) in program.movement"
                        >
                            <template v-if="movement.type == '1'">
                                <div
                                    class="flex items-center justify-between mb-1"
                                >
                                    <div class="flex items-center">
                                        <div
                                            class="text-sm font-bold flex items-center"
                                            v-for="(
                                                item, j
                                            ) in movement.movement"
                                            :key="j"
                                        >
                                            <span>{{
                                                item?.movement?.name
                                            }}</span>
                                            <span class="mr-1 text-gray-500"
                                                >({{
                                                    item?.movement_type?.name
                                                }})</span
                                            >
                                        </div>
                                    </div>
                                    <div class="mr-2">
                                        <div
                                            class="flex items-center"
                                            v-for="(
                                                item, j
                                            ) in movement.movement"
                                            :key="j"
                                        >
                                            <template
                                                v-if="item.movement.is_aerobic"
                                            >
                                                <template
                                                    v-if="
                                                        item.movement
                                                            .is_repeater
                                                    "
                                                >
                                                    <div class="text-sm">
                                                        {{ item?.value }}
                                                        ثانیه
                                                    </div>
                                                    <span>x</span>
                                                    <div class="text-sm">
                                                        {{ item?.repeat }}
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <div class="text-sm">
                                                        {{ item?.value }}
                                                        ثانیه
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div class="text-sm font-bold">
                                                    {{ item?.value }}
                                                </div>
                                                <span>x</span>
                                                <div class="text-sm font-bold">
                                                    {{ item?.repeat }}
                                                </div>
                                                <div
                                                    class="font-bold mr-1"
                                                    v-if="item?.practise >= 1"
                                                >
                                                    / {{ item?.practise }}
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="movement.type == '2'">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="flex items-center"
                                            v-for="(
                                                item, j
                                            ) in movement.movement"
                                            :key="j"
                                        >
                                            <div class="text-sm font-bold">
                                                <span class="">{{
                                                    item.movement?.name
                                                }}</span>

                                                <span
                                                    class="mr-1 text-gray-800 font-bold"
                                                    >({{ j + 1 }})</span
                                                >
                                            </div>
                                            <span class="mx-1" v-if="j == 0"
                                                >x</span
                                            >
                                        </div>
                                    </div>

                                    <div class="flex items-center mr-2">
                                        <div>
                                            <div
                                                class="flex flex-col items-center"
                                                v-for="(
                                                    item, j
                                                ) in movement.movement"
                                                :key="j"
                                            >
                                                <div
                                                    class="flex items-center text-sm border-b-2"
                                                >
                                                    <span
                                                        class="ml-1 text-gray-800 font-bold"
                                                        >({{ j + 1 }})</span
                                                    >
                                                    <span>{{
                                                        item.value
                                                    }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="mx-1">x</span>
                                        <div class="text-sm">
                                            {{ movement.movement[0].repeat }}
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-if="movement.type == '3'">
                                <div class="flex items-center justify-between">
                                    <div
                                        class="flex items-center"
                                        v-for="(item, j) in movement.movement"
                                        :key="j"
                                    >
                                        <div class="text-sm font-bold">
                                            <span>
                                                {{ item.movement?.name }}
                                            </span>
                                            <span class="mr-1 text-gray-500"
                                                >({{
                                                    item?.movement_type?.name
                                                }})</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center mr-2"
                                        v-for="(item, j) in movement.movement"
                                        :key="j"
                                    >
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="flex items-center text-sm border-b-2"
                                                v-for="(
                                                    movement_value, k
                                                ) in item.values"
                                                :key="k"
                                            >
                                                <span>{{
                                                    movement_value.value
                                                }}</span>
                                            </div>
                                        </div>
                                        <span class="mx-1">x</span>
                                        <div class="text-sm">
                                            {{ item.repeat }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="movement.type == '4'">
                                <div class="flex items-center justify-between">
                                    <div
                                        class="flex items-center"
                                        v-for="(item, j) in movement.movement"
                                        :key="j"
                                    >
                                        <div class="text-sm font-bold">
                                            <span>
                                                {{ item.movement?.name }}</span
                                            >
                                            <span class="mr-1 text-gray-500"
                                                >(
                                                {{
                                                    item?.movement_type?.name
                                                }})</span
                                            >
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center mr-2 dir-ltr"
                                        v-for="(item, j) in movement.movement"
                                        :key="j"
                                    >
                                        <div
                                            v-for="(
                                                movement_value, k
                                            ) in item.values"
                                            :key="k"
                                            class="flex items-center zzz"
                                        >
                                            <span class="mx-1 divider-value"
                                                >-</span
                                            >
                                            <div
                                                class="text-sm flex flex-col items-center"
                                            >
                                                <span class="border-b-2">{{
                                                    movement_value.value
                                                }}</span>
                                                <!-- <span>{{
                                                    movement_value.repeat
                                                }}</span> -->
                                            </div>
                                        </div>
                                        <div
                                            class="font-bold mr-1"
                                            v-if="item?.practise >= 1"
                                        >
                                            /
                                            {{ item?.practise }}
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue";
import { ref } from "vue";
import ApiService from "@/Core/services/ApiService";
import { useRoute } from "vue-router";
import html2pdf from "html2pdf.js";
const formRef = ref(null);
const loader = ref(false);
const route = useRoute();
const form_programs = ref([
    { class: "1", movement: [] },
    { class: "2", movement: [] },
    { class: "3", movement: [] },
    { class: "4", movement: [] },
]);
const form = ref({});
const fetchData = async () => {
    const { data } = await ApiService.get(
        `/api/panel/form/${route.params.id}/detail`
    );
    form.value = data.data;
    form.value.programs.map((program_item, j) => {
        // console.log("program", program);
        // form_programs.value[index] = program;
        let formated_movement = {
            type: "1",
            movement: [],
            class: program_item.class,
        };
        let selected_program = form_programs.value.find(
            (form_program, k) => form_program.class == program_item.class
        );
        program_item.movements.map((item, index) => {
            // created_movements.value.push({ type: selected_type });
            formated_movement.type = item.type;
            if (item.type == "1") {
                let formated_movement = { type: item.type, movement: [] };
                item.programs.map((program, j) => {
                    formated_movement.movement.push({
                        movement: program.movement,
                        movement_type: program.exercise,
                        value: program.value,
                        repeat: program.repeat,
                        practise: program.practise,
                        title: program.title,
                    });
                });
                selected_program.movement.push(formated_movement);
            } else if (item.type == "2") {
                let formated_movement = { type: item.type, movement: [] };
                item.programs.map((program, j) => {
                    formated_movement.movement.push({
                        movement: program.movement,
                        movement_type: program.exercise,
                        value: program.value,
                        repeat: program.repeat,
                        practise: program.practise,
                        title: program.title,
                    });
                });
                selected_program.movement.push(formated_movement);
            } else if (item.type == "3") {
                let formated_movement = { type: item.type, movement: [] };
                formated_movement.movement.push({
                    movement: item.programs[0].movement,
                    movement_type: item.programs[0].exercise,
                    value: item.programs[0].value,
                    repeat: item.programs[0].repeat,
                    practise: item.programs[0].practise,
                    title: item.programs[0].title,
                    values: [],
                });
                item.programs.map((program, j) => {
                    formated_movement.movement[0]["values"].push({
                        repeat: program.repeat,
                        value: program.value,
                    });
                });
                selected_program.movement.push(formated_movement);
            } else if (item.type == "4") {
                let formated_movement = { type: item.type, movement: [] };
                formated_movement.movement.push({
                    movement: item.programs[0].movement,
                    movement_type: item.programs[0].exercise,
                    value: item.programs[0].value,
                    repeat: item.programs[0].repeat,
                    practise: item.programs[0].practise,
                    title: item.programs[0].title,
                    values: [],
                });
                item.programs.map((program, j) => {
                    formated_movement.movement[0]["values"].push({
                        repeat: program.repeat,
                        value: program.value,
                    });
                });
                selected_program.movement.push(formated_movement);
            }
        });
        // form_programs.value[j] = formated_movement;
    });
};

const convertToPdf = () => {
    loader.value = true;
    const element = formRef.value.cloneNode(true);
    element.classList.add("invoice-container");
    var opt = {
        // margin: 0,
        filename: `form.pdf`,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: "in", format: "a4", orientation: "p" },
    };
};

onMounted(() => {
    fetchData().then(() => {});
});
</script>

<style>
.dir-ltr {
    direction: ltr !important;
}
</style>
