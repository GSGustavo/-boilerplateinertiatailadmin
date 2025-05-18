<script>

import { ref, reactive, onMounted, onUnmounted, useTemplateRef } from 'vue'
import { Link, router, Head, useForm, usePage } from '@inertiajs/vue3';
import MainAdmin from '@/Components/layout/MainAdmin.vue';
import PageBreadcrumb from '@/Components/common/PageBreadcrumb.vue'
import ComponentCard from "@/Components/common/ComponentCard.vue";
import BasicTableOne from "@/Components/tables/basic-tables/BasicTableOne.vue";
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SvgIcon from '@jamescoyle/vue-icon'
import { mdiPlus, mdiClose, mdiCheck, mdiSortAscending, mdiSortDescending, mdiFilterMultipleOutline, mdiMenuDown, mdiMenuUp, mdiCheckCircleOutline, mdiCloseCircleOutline } from '@mdi/js';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { useWindowSize } from '@vueuse/core'
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from 'zod'
import { FormField } from '@primevue/forms';


export default {
    layout: MainAdmin,
    components: {
        Head, PageBreadcrumb, ComponentCard, BasicTableOne, InputLabel, TextInput, SvgIcon
    },
    data() {
        return {
            mdiPlus,
            mdiClose,
            mdiCheck,
            mdiSortAscending,
            mdiSortDescending,
            mdiFilterMultipleOutline,
            mdiMenuDown,
            mdiMenuUp,
            mdiCheckCircleOutline,
            mdiCloseCircleOutline,
        }
    },
    methods: {

    },
    setup() {

        const toast = useToast();
        const currentPageTitle = ref('Planos')
        const loading = ref(false);
        const routeToSave = ref(route("backoffice.admin.plans.save"));
        const routeToGet = ref(route("api.backoffice.admin.plans.get"));
        const routeToDeletePlanItems = ref(route("backoffice.admin.planitems.delete"));

        const localClinicaId = ref(localStorage.getItem("clinica_id"));

        const users = ref([]);



        const getItems = () => {
            loading.value = true;

            const itemToPost = {};

            if (localClinicaId.value) {
                itemToPost.clinica_id = localClinicaId.value;
            };

            axios.post(routeToGet.value, { ...itemToPost })
                .then((response) => {

                    if (!response.data.hasErrors) {
                        items.value = response.data.items;
                        loading.value = false;
                        toast.add({ severity: "success", summary: "Sucesso!", detail: "Itens recarregados.", life: 5000 })
                    } else {
                        toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                    }

                }).catch(function (error) {
                    toast.add({ severity: "error", summary: "Erro!", detail: "Houve um erro tente novamente mais tarde.", life: 5000 })
                })
        }

        const items = ref([]);

        const item = ref({
            id: null,
            status: true,
            internal: false,
            name: '',
            price: '',
            days: '',
        });

        onMounted(() => {
            getItems();

        });

        const dt = ref();

        const itemDialog = ref(false);
        const submitted = ref(false);
        const deleteItemDialog = ref(false);
        const deleteItemsDialog = ref(false);

        const selectedItems = ref();
        const filters = ref({
            'global': { value: null, matchMode: FilterMatchMode.CONTAINS },
        });

        var optionsPrice = {
            onComplete: function (price) {

            },
            onKeyPress: function (price, event, currentField, options) {

            },
            onChange: function (price) {
                item.value.price = price;
            },
            onInvalid: function (val, e, f, invalid, options) {

            },
            reverse: true
        };


        const openNew = () => {

            submitted.value = false;
            itemDialog.value = true;

            setTimeout(() => {
                $('#price').mask('000.000.000.000.000,00', optionsPrice);
            }, 500)
        };

        const hideDialog = () => {
            itemDialog.value = false;
            submitted.value = false;

            item.value = {
                id: null,
                status: true,
                internal: false,
                name: '',
                price: '',
                days: '',
            };
        };

        const saveItem = ({ valid }) => {

            submitted.value = true;

            if (valid) {

                const itemToPost = { ...item.value, operation: 1 }

                if (localClinicaId.value) {
                    itemToPost.clinica_id = localClinicaId.value;
                };

                itemToPost.planitems = JSON.stringify(planitems.value)

                axios.post(routeToSave.value, itemToPost)
                    .then((response) => {

                        const data = response.data;

                        if (!data.hasErrors) {

                            item.value.created_at = data.item.created_at;
                            item.value.updated_at = data.item.updated_at;

                            if (!item.value.id) {
                                item.value.id = data.item.id;
                                items.value.push(item.value);
                            }
                           
                            const itemIndex = findIndexById(item.value.id);

                            items.value[itemIndex] = item.value;
                            items.value[itemIndex].plan_items = data.planitems

                            let count = 0;

                            data.alerts.forEach((el) => {
                                toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });

                                count += 1000
                            })

                            hideDialog();

                        }
                    }).catch(function (error) {
                        toast.add({ severity: "error", summary: "Erro!", detail: error, life: 5000 })
                    })
            }
        };

        const editItem = (data) => {


            item.value.id = data.id
            item.value.name = data.name

            item.value.price = data.price
            item.value.days = data.days
            item.value.sobre_descricao = data.sobre_descricao
            item.value.status = data.status
            item.value.internal = data.internal

            planitems.value = data.plan_items;

            itemDialog.value = true;


        };

        const confirmDeleteItem = (prod) => {
            item.value = prod;
            deleteItemDialog.value = true;
        };

        const deleteItem = () => {
            items.value = items.value.filter(val => val.id !== item.value.id);
            deleteItemDialog.value = false;
            item.value = {};
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Product Deleted', life: 3000 });
        };

        const findIndexById = (id) => {
            let index = -1;
            for (let i = 0; i < items.value.length; i++) {
                if (items.value[i].id === id) {
                    index = i;
                    break;
                }
            }

            return index;
        };

        const exportCSV = () => {
            dt.value.exportCSV();
        };

        const confirmDeleteSelected = () => {
            deleteItemsDialog.value = true;
        };

        const deleteSelectedItems = () => {
            items.value = items.value.filter(val => !selectedItems.value.includes(val));
            deleteItemsDialog.value = false;
            selectedItems.value = null;
            toast.add({ severity: 'success', summary: 'Successful', detail: 'Products Deleted', life: 3000 });
        };

        const getStatusLabel = (status) => {
            switch (status) {
                case true:
                    return 'success';
                default:
                    return 'danger'
            }
        };

        const width = useWindowSize().width


        const resolver = ref(zodResolver(
            z.object({
                days: z.number({
                    required_error: "Os dias é obrigatório!",
                    invalid_type_error: "Os dias devem ser um número!",
                }),
                name: z.string().min(1, { message: 'O nome é obrigatório' }),
                price: z.string({
                    required_error: "O preço é obrigatório.",
                    invalid_type_error: "O preço é inválido!",
                }).min(1, { message: 'O preço é obrigatório' }),
            })
        ))

        const planitems = ref([]);
        const planitemSubmitted = ref(false);
        const planitem = ref("");

        const addPlanItem = () => {
            planitemSubmitted.value = true

            if (planitem.value) {

                planitems.value.unshift({
                    id: null,
                    name: planitem.value,
                })
                planitem.value = ""
                planitemSubmitted.value = false
            }
        }

        const confirmDeletePlanItemModal = ref(false);
        const planItemDeleteIndex = ref(null);

        const deletePlanItem = () => {

            const index = planItemDeleteIndex.value;

            // Se tiver ID é pq ja esta no banco, se não, é local, então posso tirar do array

            const id = planitems.value[index].id

            if (id) {
                axios.post(routeToDeletePlanItems.value, { id: id }).
                    then((response) => {

                        const data = response.data;

                        if (!data.hasErrors) {

                            hidePlanItemDeleteDialog()

                            planitems.value = planitems.value.filter((el, i) => i !== index)

                            const itemIndex = findIndexById(item.value.id);

                            items.value[itemIndex].plan_items = planitems.value

                            let count = 0;

                            data.alerts.forEach((el) => {
                                toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });
                                count += 1000
                            })

                        } else {

                            let count = 0;

                            data.errors.forEach((el) => {
                                toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });
                                count += 1000
                            })

                        }
                    }).catch((error) => {

                        const errorResponse = error.response.data.errors;
                        let count = 0;

                        errorResponse.forEach((el) => {
                            toast.add({ severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count });

                            count += 1000
                        })
                    })
            } else {

                planitems.value = planitems.value.filter((el, i) => i !== index)
                toast.add({ severity: 'success', summary: 'Sucesso!', detail: "Operação feita com sucesso!", life: 5000 });

                hidePlanItemDeleteDialog()
            }

        }

        const hidePlanItemDeleteDialog = () => {
            confirmDeletePlanItemModal.value = false
            planItemDeleteIndex.value = null
        }

        return {
            hidePlanItemDeleteDialog,
            planItemDeleteIndex,
            deletePlanItem,
            confirmDeletePlanItemModal,
            addPlanItem,
            planitemSubmitted,
            planitems,
            planitem,
            resolver,
            currentPageTitle,
            items,
            filters,
            openNew,
            hideDialog,
            saveItem,
            editItem,
            confirmDeleteItem,
            deleteItem,
            exportCSV,
            confirmDeleteSelected,
            deleteSelectedItems,
            getStatusLabel,
            item,
            deleteItemDialog,
            itemDialog,
            dt,
            selectedItems,
            loading,
            getItems,
            submitted,
            localClinicaId,
            users,
            width
        }
    },

}

</script>

<style scoped>
.fade-move,
.fade-enter-active,
.fade-leave-active {
    transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(30px);
}
</style>

<template>

    <Head>
        <title>{{ currentPageTitle }}</title>
        <meta name="description" content="Aqui você controlará todos seus planos!">
    </Head>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <Toast />

    <Dialog v-model:visible="confirmDeletePlanItemModal" :style="{ width: '650px' }" header="Tem certeza?"
        :modal="true">
        <div class="flex flex-col gap-6">
            Tem certeza que deseja apagar o item?
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="confirmDeletePlanItemModal = false" />
            <Button label="Sim" icon="pi pi-trash" severity="danger" @click="deletePlanItem" />
        </template>
    </Dialog>

    <Dialog v-on:hide="hideDialog" :dismissableMask="true" v-model:visible="itemDialog" header="Detalhes do Plano"
        class="z-9999999" :modal="true">
        <Form ref="formRef" v-slot="$form" :resolver="resolver" :initialValues="item" @submit="saveItem"
            class="flex flex-col justify-center gap-4">
            <div class="flex gap-4 2xsm:flex-col lg:flex-row">
                <div class="flex flex-col gap-6 w-[400px]">
                    <div class="col-span-6">
                        <label for="name" class="block font-bold mb-3">Nome</label>
                        <InputText name="name" placeholder="Ex.: Plano Essencial" id="name" v-model.trim="item.name"
                            autofocus fluid />
                        <Message v-if="$form.name?.invalid" severity="error" size="small" variant="simple">{{
                            $form.name.error?.message }}</Message>
                    </div>
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-6">

                            <label for="price" class="block font-bold mb-3">Preço</label>

                            <InputGroup v-tooltip="'0 para plano grátis.'">
                                <InputGroupAddon>R$</InputGroupAddon>
                                <InputText placeholder="14,99" id="price" name="price" v-model.trim="item.price"
                                    fluid />
                            </InputGroup>


                            <Message v-if="$form.price?.invalid" severity="error" size="small" variant="simple">{{
                                $form.price.error?.message }}</Message>
                        </div>
                        <div class="col-span-6">
                            <label for="days" class="block font-bold mb-3">Dias</label>
                            <InputNumber v-tooltip="'0 para plano infinito.'" name="days" placeholder="Ex.: 30"
                                id="days" v-model.trim="item.days" autofocus fluid />
                            <Message v-if="$form.days?.invalid" severity="error" size="small" variant="simple">{{
                                $form.days.error?.message }}</Message>
                        </div>
                    </div>


                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-6">
                            <label class="">
                                <span class="block font-bold mb-4">Ativo?</span>
                                <ToggleSwitch name="status" v-model="item.status" />
                            </label>
                        </div>
                        <div class="col-span-6">
                            <label class=""
                                v-tooltip="'É um plano interno, como um plano exclusivo e que não vai aparecer no site.'">
                                <span class="block font-bold mb-4">Plano Interno?</span>
                                <ToggleSwitch name="internal" v-model="item.internal" />
                            </label>
                        </div>
                    </div>
                    <!-- <div>
                        <label class="">
                            <span class="block font-bold mb-4">Ativo?</span>
                            <ToggleSwitch name="status" v-model="item.status" />
                        </label>
                    </div> -->
                </div>
                <!-- Divider -->
                <Divider v-if="width >= 600" layout="vertical" />
                <div class="flex flex-col gap-6 w-[400px]">
                    <div class="grid grid-cols-8 gap-4">
                        <div class="col-span-5 grid grid-rows-2">
                            <label for="start_on" class="block font-bold">Itens</label>
                            <InputText placeholder="Escreva o Item aqui" v-model="planitem" class="w-full" />
                            <small v-if="planitemSubmitted && !planitem" class="text-red-500">Preencha o
                                item
                                para
                                adicionar.</small>
                        </div>
                        <div class="col-span-3 grid grid-rows-2">
                            <Button class="row-start-2" label="Adicionar" @click="addPlanItem" icon="pi pi-check" />
                        </div>
                    </div>

                    <div class="max-h-[400px] overflow-y-scroll">
                        <template v-for="(planitem, i) in planitems">
                            <div class="flex justify-between my-2">
                                <p>
                                    {{ planitem.name }}
                                </p>
                                <div class="flex items-center gap-4">

                                    <Button severity="danger" icon="pi pi-trash" outlined rounded class="mr-2"
                                        @click="planItemDeleteIndex = i; confirmDeletePlanItemModal = true" />

                                </div>

                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 ms-auto">
                <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Salvar" type="submit" icon="pi pi-check" />
            </div>

        </Form>

    </Dialog>

    <div class="overflow-hidden border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] min-h-[400px]">
        <Toolbar class="mb-6">
            <template #start>
                <Button label="Novo" icon="pi pi-plus" class="mr-2" @click="openNew" />
                <!-- <Button label="Delete" icon="pi pi-trash" severity="danger" outlined @click="confirmDeleteSelected"
                    :disabled="!selectedItems || !selecteditems.length" /> -->
            </template>

            <template #end>
                <!-- <FileUpload mode="basic" accept="image/*" :maxFileSize="1000000" label="Import" customUpload
                    chooseLabel="Import" class="mr-2" auto :chooseButtonProps="{ severity: 'secondary' }" />
                <Button label="Export" icon="pi pi-upload" severity="secondary" @click="exportCSV($event)" /> -->
                <Button label="Recarregar" icon="pi pi-refresh" severity="secondary" @click="getItems" />
            </template>
        </Toolbar>
        <DataTable ref="dt" v-model:selection="selectedItems" :value="items" dataKey="id" :paginator="true"
            :rows="$page.props.defaultEntries" :filters="filters"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            :rowsPerPageOptions="$page.props.maxEntries"
            currentPageReportTemplate="Mostrando {first} até {last} de {totalRecords} itens" :loading="loading">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">Gerenciar Planos</h4>
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Procurar..." />
                    </IconField>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />
            <Column field="name" header="Plano" sortable style="min-width: 16rem" />

            <Column field="internal" header="Interno" sortable style="min-width: 12rem">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.internal ? 'Sim' : 'Não'"
                        :severity="getStatusLabel(slotProps.data.internal)" />
                </template>
            </Column>


            <Column field="status" header="Status" sortable style="min-width: 12rem">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.status ? 'Ativo' : 'Inativo'"
                        :severity="getStatusLabel(slotProps.data.status)" />
                </template>
            </Column>

            <Column field="updated_at" header="Atualizado em" sortable dataType="date" style="min-width: 10rem" />
            <Column field="created_at" header="Criado em" sortable dataType="date" style="min-width: 10rem" />

            <Column header="Ações" :exportable="false" style="min-width: 12rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editItem(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

    </div>

</template>
