<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <chat-room-selection
                  v-if="currentRoom"
                  :rooms="chatRooms"
                  :currentroom="currentRoom"
                  v-on:romchanged="setRoom($event)"
                />
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                   <message-container :messages="messages"/>
                   <input-messgae :room="currentRoom" v-on:sendmessage="getMessages()"/>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import MessageContainer from './messageContainer.vue'
    import InputMessgae from './inputMessgae.vue'
    import ChatRoomSelection from './chatRoomSelection.vue'


    export default {
        components: {
            AppLayout,
            MessageContainer,
            InputMessgae,
            ChatRoomSelection,
        },

        data(){
            return{
              chatRooms: [],
              currentRoom: [],
              messages: [],
            }
        },

        watch: {
            currentRoom(){
              this.connect();
            }
        },
        methods: {
            connect(){
               if(this.currentRoom.id){
                   let vm = this;
                    this.getMessages();
                    window.Echo.private("chat."+this.currentRoom.id)
                    .listen('.message.new', e => {
                        vm.getMessages();
                    })
               }
            },
            getRooms(){
                axios.get('/chat/rooms')
                .then(response => {
                    this.chatRooms = response.data;
                    this.setRoom(response.data[0]);
                })
                .catch( error => {
                  console.log( error );
                });
            },

            setRoom( room ){
                this.currentRoom = room;

            },

            getMessages(){
                axios.get('/chat/rooms/'+this.currentRoom.id+'/messages')
                .then(response => {
                    this.messages = response.data;
                })
                .catch(error =>{
                   console.log(error);
                });
            }
        },

        created(){
            this.getRooms();
        },
    }
</script>

