<template>
    <div class="addon-container" :class="{'iframe-page-addon':iframe}">
        <div class="addon">
            <h3 class="addon__title">{{addon.name}}</h3>
            <div class="addon__text">{{schoolName}}</div>
            <div class="addon__controls">
                <div class="addon__quantity">
                  <i class="addon__minus fas fa-minus" @click="remove"></i>
                    <input type="text" class="addon__number" :value="quantity" readonly>
                  <i class="addon__plus fas fa-plus" @click="add"></i>
                </div>
                <div class="addon_price">
                    <div class="course-box__sum">{{displayPrice}} kr</div>
                </div>
                <div class="addon__delete" @click="deleteHandle">
                    <div class="addon__trash"></div>
                    <div class="addon__remove" >Ta bort</div>
                </div>
            </div>
        </div>
        <div class="school-info">
            <ul class="course-item-list" v-if="description">
                <li class="course-item-list-item" v-for="line in computeLines(description)">{{line}}</li>
            </ul>
        </div>
    </div>
</template>

<script>
    import Icon from 'vue-components/Icon';

    export default {
        components: {
            Icon,
        },
        props: ['addon', 'onAdd', 'onRemove', 'onDelete', 'custom', 'quantity', 'schoolName', 'iframe'],
        data() {
            return {
                //quantity: 0,
                onAddCallback: () => { },
                onRemoveCallback: () => { },
                onDeleteCallback: () => { },
            };
        },
        computed: {
            description() {
                if (this.addon) {
                    return this.custom ? this.addon.description : this.addon.pivot.description;
                }
            },
            price() {
                if (this.addon) {
                    return this.custom ? this.addon.price : this.addon.pivot.price;
                }
            },
            displayPrice() {
                if (this.addon) {
                    return this.price * this.quantity;
                }
            }
        },
        methods: {
            remove() {
                this.onRemoveCallback(this.addon);
            },
            add() {

                this.onAddCallback(this.addon);
            },
            deleteHandle() {
                this.onDeleteCallback(this.addon);
            },
            computeLines(text) {
                let linesArr = text.split('\n');
                linesArr = linesArr.filter((elm) => {
                    return elm.length;
                });
                return linesArr;
            },
        },
        created() {
            this.onAddCallback = this.onAdd(this.custom);
            this.onRemoveCallback = this.onRemove(this.custom);
            this.onDeleteCallback = this.onDelete(this.custom);
        }
    }
</script>
