<template>
    <ul class="pagination" v-if="pagination.last_page > 1" :class="customClass">
        <li class="page-item" :class="{'disabled': pagination.current_page == 1}">
            <span class="disabled page-link" v-if="pagination.current_page == 1"><i class="fa fa-caret-left"></i></span>
            <a class="page-link" href="javascript:void(0)" aria-label="Previous" @click.prevent="changePage(pagination.current_page - 1)" v-else>
                <i class="fa fa-caret-left"></i>
            </a>
        </li>
        <li class="page-item" v-for="(page, index) in pagesNumber" :class="{'active': page == pagination.current_page}" :key="index">
            <a class="page-link" href="javascript:void(0)" @click.prevent="changePage(page)">{{ page }}</a>
        </li>
        <li class="page-item" :class="{'disabled': pagination.current_page == pagination.last_page}">
            <span class="disabled page-link" v-if="pagination.current_page == pagination.last_page"><i class="fa fa-caret-right"></i></span>
            <a class="page-link" href="javascript:void(0)" aria-label="Next" @click.prevent="changePage(pagination.current_page + 1)" v-else>
                <i class="fa fa-caret-right"></i>
            </a>
        </li>
    </ul>
</template>

<script>
    export default {
        name:     "Pagination",
        props:    {
            pagination: {
                type:     Object,
                required: true
            },
            offset:     {
                type:    Number,
                default: 5
            },
            customClass: {
                type: String,
            },
        },
        computed: {
            pagesNumber() {
                if (!this.pagination.to) {
                    return [];
                }

                let from = this.pagination.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }

                let to = this.offset;
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }

                let pagesArray = [];
                for (let page = from; page <= to; page++) {
                    pagesArray.push(page);
                }

                return pagesArray;
            }
        },
        methods:  {
            changePage(page) {
                this.pagination.current_page = page;
                this.$emit('paginate', page);
            }
        }
    }
</script>
