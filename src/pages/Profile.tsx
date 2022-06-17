import axios from 'axios'
import React, { useState, useEffect } from 'react'
import '../App.css'
import { useParams } from 'react-router-dom';
import UserData from '../components/UserData';
import CreateJob from '../components/CreateJob'

import store from '../store/store'
import {SetUserData as setUserDataRedux} from '../store/UserData/action'


interface User{
    Username:""
    Email:""
    Birthday:""
}

interface Props{
    userLoggedId:string
}

function Profile(props:Props) {

    const userId = useParams();
    const [userData, setUserData] = useState<User>();

    useEffect(() => {
        getData();
      });

    const  getData = () =>{
        axios.get("http://localhost:80/pr1/php/api/usuaris/perfil/" + userId.id)
        .then((r)=>  
            setUserData(r.data[0])
            // store.dispatch(setUserDataRedux(r.data[0].Username,r.data[0].Email,`http://localhost:80/pr1/php/api/images/fotos_perfil_upload/${r.data[0].User_id}.jpg`,r.data[0].Birthday))
        )
        // .catch(error => console.log(error))
        // console.log(store.getState())
    }




    return (
        <div className='profile'>
            <br />
            <UserData
                // Username={store.getState().userDataReducer.Username} 
                // Email={store.getState().userDataReducer.Email}
                // Src={store.getState().userDataReducer.Src}
                // Birthday={store.getState().userDataReducer.Birthday}
                
                Username={userData?.Username} 
                Email={userData?.Email}
                Src={`http://localhost:80/pr1/php/api/images/fotos_perfil_upload/${userId.id}.jpg`}
                Birthday={userData?.Birthday}
            />
            {
                props.userLoggedId.toString() === userId.id.toString() ?
                <div>
                    {/* <CreateJob></CreateJob> */}
                    <h1>hola</h1>
              
                </div> 
                :
                <h1>no</h1>
            }

        </div>
    )
}

export default Profile

