<template>
    <div>
        <v-sheet>
            <div class="mb-6">
                <h2 class="text-xl">مدیریت تمرینات</h2>
            </div>

            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-12 xl:col-span-4 lg:col-span-4">
                    <v-select
                        v-model="form.type"
                        label="انتخاب نوع ست"
                        :items="types"
                        item-title="state"
                        item-value="value"
                        return-object
                        single-line
                        variant="solo-filled"
                    ></v-select>

                    <template v-if="form.type.value == '1'">
                        <NormalMovement
                            :movement-values="movement_values"
                            :movement-data="movements"
                            :exercise-data="exercises"
                            @change-movement="handleOnChangeMovement"
                        />
                    </template>
                    <template v-if="form.type.value == '2'">
                        <SuperSetMovement
                            :movement-values="movement_values"
                            :movement-data="movements"
                            :exercise-data="exercises"
                        />
                    </template>

                    <template v-if="form.type.value == '3'">
                        <DropSetMovement
                            :movement-values="movement_values"
                            :movement-data="movements"
                            :exercise-data="exercises"
                            @add-value="handleIncMovement"
                            @delete-value="deleteMovementValue"
                        />
                    </template>
                    <template v-if="form.type.value == '4'">
                        <PyramidalMovement
                            :movement-values="movement_values"
                            :movement-data="movements"
                            :exercise-data="exercises"
                            @add-value="handleIncMovement"
                            @delete-value="deleteMovementValue"
                        />
                    </template>

                    <div>
                        <v-btn
                            @click="handleSaveMovement"
                            color="light-blue-accent-4"
                            block
                            class="mt-2"
                            >ذخیره</v-btn
                        >
                    </div>
                </div>
                <div class="col-span-12 xl:col-span-8 lg:col-span-8 px-4">
                    <div
                        class="bg-gray-200 px-3 py-3 mb-2 rounded-lg"
                        v-for="(movement, index) in created_movements"
                    >
                        <template v-if="movement.type.value == '1'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="text-sm text-gray-600"
                                        v-for="(item, j) in movement.movement"
                                        :key="j"
                                    >
                                        <span>{{ item?.movement?.name }}</span>
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
                                    <template v-if="item.movement.is_aerobic">
                                        <template
                                            v-if="item.movement.is_repeater"
                                        >
                                            <div class="text-sm">
                                                {{ item?.value }} ثانیه
                                            </div>
                                            <span>x</span>
                                            <div class="text-sm">
                                                {{ item?.repeat }}
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="text-sm">
                                                {{ item?.value }} ثانیه
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="text-sm">
                                            {{ item?.value }}
                                        </div>
                                        <span>x</span>
                                        <div class="text-sm">
                                            {{ item?.repeat }}
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template v-if="movement.type.value == '2'">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div
                                        class="flex items-center"
                                        v-for="(item, j) in movement.movement"
                                        :key="j"
                                    >
                                        <div class="text-sm text-gray-600">
                                            <span>{{
                                                item.movement?.name
                                            }}</span>
                                            <span class="mr-1 text-gray-500"
                                                >(
                                                {{
                                                    item?.movement_type?.name
                                                }})</span
                                            >
                                            <span
                                                class="mr-1 text-gray-800 font-bold"
                                                >({{ j + 1 }})</span
                                            >
                                        </div>
                                        <span class="mx-2" v-if="j == 0"
                                            >x</span
                                        >
                                    </div>
                                </div>
                                <div class="flex items-center mr-2">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="flex items-center text-sm border-b-2"
                                            v-for="(
                                                item, j
                                            ) in movement.movement"
                                            :key="j"
                                        >
                                            <span
                                                class="ml-1 text-gray-800 font-bold"
                                                >({{ j + 1 }})</span
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
                                                    <span
                                                        class="flex items-center"
                                                    >
                                                        <span
                                                            >{{
                                                                item?.value
                                                            }}
                                                            ثانیه</span
                                                        >
                                                        <span class="mx-1"
                                                            >x</span
                                                        >
                                                        <span>{{
                                                            item?.repeat
                                                        }}</span>
                                                    </span>
                                                </template>
                                                <template v-else>
                                                    <div class="text-sm">
                                                        {{ item?.value }} ثانیه
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <span class="flex items-center">
                                                    <span>{{
                                                        item?.value
                                                    }}</span>
                                                    <span class="mx-1">x</span>
                                                    <span>{{
                                                        item?.repeat
                                                    }}</span>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-if="movement.type.value == '3'">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex items-center"
                                    v-for="(item, j) in movement.movement"
                                    :key="j"
                                >
                                    <div class="text-sm text-gray-600">
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
                                            <!-- <span
                                                class="ml-1 text-gray-800 font-bold"
                                                >(1)</span
                                            > -->
                                            <span>{{
                                                movement_value.value
                                            }}</span>
                                        </div>
                                    </div>
                                    <span class="mx-1">x</span>
                                    <div class="text-sm">{{ item.repeat }}</div>
                                </div>
                            </div>
                        </template>
                        <template v-if="movement.type.value == '4'">
                            <div class="flex items-center justify-between">
                                <div
                                    class="flex items-center"
                                    v-for="(item, j) in movement.movement"
                                    :key="j"
                                >
                                    <div class="text-sm text-gray-600">
                                        <span> {{ item.movement?.name }}</span>
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
                                        <span class="mx-2 divider-value"
                                            >/</span
                                        >
                                        <div
                                            class="text-sm flex flex-col items-center"
                                        >
                                            <span class="border-b-2">{{
                                                movement_value.value
                                            }}</span>
                                            <span>{{
                                                movement_value.repeat
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="col-span-12 mt-8">
                    <div>
                        <v-btn
                            @click="handleSubmit"
                            size="large"
                            block
                            class="mt-2"
                            >ذخیره تغییرات</v-btn
                        >
                    </div>
                </div>
            </div>
        </v-sheet>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from "vue";
import ApiService from "@/Core/services/ApiService";
import { ErrorMessage, Field, Form } from "vee-validate";
import NormalMovement from "@/Components/Movenment/Normal.vue";
import SuperSetMovement from "@/Components/Movenment/SuperSet.vue";
import DropSetMovement from "@/Components/Movenment/DropSet.vue";
import PyramidalMovement from "@/Components/Movenment/Pyramidal.vue";
const exercises = ref([]);
const movements = ref([]);
const searchTerm = ref("");
const created_movements = ref([]);
const search_movement = ref("");
const types = ref([
    { state: "عادی", value: "1" },
    { state: "سوپر ست", value: "2" },
    { state: " دراپ ست", value: "3" },
    { state: " هرمی", value: "4" },
]);
const form = ref({
    type: { state: "عادی", value: "1" },
});
const filteredExercises = computed(() => {
    const search = searchTerm.value.trim().toLowerCase();
    if (search === "") {
        return exercises.value;
    } else {
        return exercises.value.filter((exercise) =>
            exercise.name.toLowerCase().includes(search)
        );
    }
});
const filteredMovements = computed(() => {
    const search = search_movement.value.trim().toLowerCase();
    if (search === "") {
        return movements.value;
    } else {
        return movements.value.filter((movement) =>
            movement.name.toLowerCase().includes(search)
        );
    }
});
const movement_values = ref([
    {
        value: "",
        repeat: "",
        movement_type: null,
        movement: null,
        values: [{ value: "", repeat: "" }],
    },
]);

const handleSaveMovement = () => {
    created_movements.value.push({
        movement: movement_values.value,
        type: form.value.type,
    });
    movement_values.value = [
        {
            value: "",
            repeat: "",
            movement_type: null,
            movement: null,
            values: [{ value: "", repeat: "" }],
        },
    ];
};
const handleIncMovement = (movement) => {
    switch (form.value.type.value) {
        case "4":
        case "3":
            movement.values.push({ value: "", repeat: "" });
            break;

        default:
            break;
    }
};
const deleteMovementValue = (item) => {
    item.row.values.splice(item.index, 1);
};
watch(
    () => form.value.type,
    (val) => {
        movement_values.value = [];
        switch (val.value) {
            case "1":
                movement_values.value.push({
                    value: "",
                    repeat: "",
                    movement_type: null,
                    movement: null,
                    values: [{ value: "", repeat: "" }],
                });
                break;
            case "2":
                movement_values.value.push(
                    {
                        value: "",
                        repeat: "",
                        movement_type: null,
                        movement: null,
                        values: [{ value: "", repeat: "" }],
                    },
                    {
                        value: "",
                        repeat: "",
                        movement_type: null,
                        movement: null,
                        values: [{ value: "", repeat: "" }],
                    }
                );
                break;
            case "3":
                movement_values.value.push({
                    value: "",
                    repeat: "",
                    movement_type: null,
                    movement: null,
                    values: [{ value: "", repeat: "" }],
                });
                break;
            case "4":
                movement_values.value.push({
                    value: "",
                    repeat: "",
                    movement_type: null,
                    movement: null,
                    values: [{ value: "", repeat: "" }],
                });
                break;

            default:
                break;
        }
    }
);

const handleOnChangeMovement = (index) => {
    movement_values.value[index].values.forEach((item) => {
        item.value = "";
        item.repeat = "";
    });
};

const fetchData = async () => {
    const { data } = await ApiService.get("/api/panel/exercises");
    exercises.value = data.data;
    const { data: response } = await ApiService.get("/api/panel/movements");
    movements.value = response.data;
};
onMounted(() => {
    fetchData();
});
</script>

<style>
.zzz:first-of-type > .divider-value {
    display: none !important;
}
</style>
