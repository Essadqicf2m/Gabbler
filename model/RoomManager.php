<?php

/**
 * Class RoomManager
 */
class RoomManager extends ManagerTableAbstract implements ManagerTableInterface {

    // Selection of every input of the room table
    public function selectAll(): array {
        $sql = "SELECT * FROM room;";
        $query = $this->db->query($sql);
        // The return when there is one or more result(s)
        if($query->rowCount()){
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        // The return when there is no result
        return [];
    }

    public function viewRoomById(int $idRoom): array {

        $viewRoomFetch = "SELECT * FROM room WHERE  id_room = ?";
        $viewRoomGoFetch = $this->db->prepare($viewRoomFetch); 

        try{

            $viewRoomGoFetch->execute([$idRoom]);
            if($viewRoomGoFetch->rowCount()){
                return $viewRoomGoFetch->fetchAll(PDO::FETCH_ASSOC);
            }else{
                return [];
            }

        }

        catch(Exception $error){
            trigger_error($error->getMessage());   
        }
    } 

    public function newRoom(Room $input): boolean {
        //Room ==> to use Room Class:
        //$input ==> will hold getters results:
        //Done to add a bunch of rooms, can be modified to fit a specific need!!

        $addRoom = "INSERT INTO room (public_room, archived_room, name_room, last_activity_room) VALUES (?,?,?,?)";
        $goAddRoom = $this->db->prepare($addRoom);

        try {
            $goAddRoom->execute([
                $input->getPublicRoom(),
                $input->getArchivedRoom(),
                $input->getNameRoom(),
                $input->getLastActivityRoom(),
            ]);
            return true;
        } catch (Exception $error) {
            trigger_error($error->getMessage());
            return false;
        }  
    }

    public function updateRoom(int $idRoom, Room $input): boolean {

        $updateRoom = "UPDATE room SET not getting it = ? WHERE not getting it = ?";
        $goUpdateRoom = $this->db->prepare($updateRoom);

        $goUpdateRoom->bindValue(2,$input->getPublicRoom(),PDO::PARAM_STR);
        $goUpdateRoom->bindValue(3,$input->getArchivedRoom(),PDO::PARAM_STR);  
        $goUpdateRoom->bindValue(4,$input->getNameRoom(),PDO::PARAM_STR);
        $goUpdateRoom->bindValue(5,$input->getLastActivityRoom(),PDO::PARAM_STR);   
        return $goUpdateRoom->execute();   

    }

    public function updateActivityRoom(int $idRoom): boolean {

        $updateActivityRoom = "UPDATE room SET last_activity_room = ? WHERE id_room = ?";
        $goUpdateActivityRoom = $this->db->prepare($updateActivityRoom);

        $goUpdateActivityRoom->bindValue($input->getLastActivityRoom(),PDO::PARAM_STR);
        $goUpdateActivityRoom->bindValue($input->getIdRoom(),PDO::PARAM_STR);
        return $goUpdateActivityRoom->execute();

    }

    public function archivedRoom(int $idRoom): boolean {


    }

    public function deadRoom(int $idRoom, int $time): boolean {


    }

    public function nameGenerateRoom(int $idRoom, Room $input): string {


    }
}