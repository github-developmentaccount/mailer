<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    /* Library Class: Imap */

    class Imap {

  
        // Open IMAP connection

        function cimap_open($username, $password, $stream)
        {
             return @imap_open("{imap.gmail.com:993/imap/ssl}{$stream}", $username, $password);
            
        }

        // Find number of msg in mailbox

        function cimap_num_msg($imap_connection)
        {
            return imap_num_msg($imap_connection);
        }

        // Find disk quota amount

        function cimap_get_quota($imap_connection)
        {
            $storage = $quota['STORAGE']= imap_get_quotaroot($imap_connection, "INBOX");

            function kilobyte($filesize)
            {
                return round($filesize / 1024, 2) . ' Mb';
            }

            return kilobyte($storage['usage']) . ' / ' . kilobyte($storage['limit']) . ' (' . round($storage['usage'] / $storage['limit'] * 100, 2) . '%)';
        }    
        function cimap_search($imap_stream)
        {
            $emails = imap_search($imap_stream, 'ALL');

                if($emails)
                {

                    $output = [];

                     rsort($emails);

                     ///////// Получение первых десяти писем///////////////////////////
                     if(count($emails) > 10){
                        $emails = array_slice($emails, 0, 10);
                        
                     }



                        foreach($emails as $msg_number)
                            {
                               
                                
                                $headerArr = imap_headerinfo ($imap_stream, $msg_number);
                                preg_match('/(.*)\+/', $headerArr->date, $date);
                                $mailArr[] =
                                 [ 
                                  'sender' => $headerArr->sender[0]->mailbox . "@" . $headerArr->sender[0]->host,
                                  'to' => $headerArr->to[0]->mailbox . "@" . $headerArr->to[0]->host,
                                  'date' => $date[1],
                                  'size' => $headerArr->Size,
                                  'subject' => self::get_decoded_headers($headerArr->subject),
                                  'unseen' =>$headerArr->Unseen,
                                  'mid' => trim($headerArr->Msgno)
                                 ];


                            }

                            return $mailArr;
                        
                }


        }

        public function get_decoded_headers($enc)
        {

                $parts = imap_mime_header_decode($enc);
                $str='';
                
                for ($p=0; $p<count($parts); $p++) {
                    $ch=$parts[$p]->charset;
                    $part=$parts[$p]->text;
                    if ($ch!=='default') $str.=mb_convert_encoding($part,'UTF-8',$ch);
                                    else $str.=$part;
                }
                return $str;
        }

        public function get_headers($imap_stream, $mid)
        {
             $result =  imap_fetchstructure($imap_stream,$mid);
            
            return $result;
        }

        public function show_message($imap_stream,$mid)
        {

          

            $structure = imap_fetchstructure($imap_stream, $mid);

            if(!isset($structure->parts)) return self::fetch_plain($imap_stream, $mid, $structure); 

            $info = self::flattenParts($structure->parts);
         
            foreach($info as $key=>$value)
                {
                    if($info[$key]->subtype == 'HTML')
                        {
                            $std[] = ['encoding'=>$info[$key]->encoding,
                                      'charset'=>$info[$key]->parameters[0]->value, 
                                      'part'=>$key];
                                      break;
                        }
                        
                }

             if($std)
                {
                    $body = imap_fetchbody($imap_stream, $mid, $std[0]['part']);
                    switch($std[0]['encoding'])
                        {
                            case '3':
                            $body = imap_base64($body);
                            break;

                            case '4': 
                            $body = imap_qprint($body);
                            break;

                        }

                    return (!preg_match('/utf-?8/i', $std[0]['charset'])) ? mb_convert_encoding($body, 'UTF-8', $charset) : $body;
                }


           
        }


        

        public function flattenParts($messageParts, $flattenedParts = array(), $prefix = '', $index = 1, $fullPrefix = true) 
        {

 

                foreach($messageParts as $part) {

                    $flattenedParts[$prefix.$index] = $part;

                    if(isset($part->parts)) {

                        if($part->type == 2) {

                            $flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix.$index.'.', 0, false);

                        }

                        elseif($fullPrefix) {

                            $flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix.$index.'.');

                        }

                        else {

                            $flattenedParts = self::flattenParts($part->parts, $flattenedParts, $prefix);

                        }
                        unset($flattenedParts[$prefix.$index]->parts);

                    }

                    $index++;

            }

            return $flattenedParts;

        }

        public function cimap_delete($inbox, $mid)
        {
                    $errors = [];
                    $check = imap_mailboxmsginfo($inbox);
                    $mid = explode(',', $mid);

                    unset($mid[count($mid)-1]);
                    if($check->Nmsgs < max($mid)) 
                        {
                            $errors[] = 'Trying to remove unexisting message!';
                        }


                        foreach($mid as $msgno)
                            {
                                if(!preg_match('/^[0-9]+$/', $msgno))
                                {
                                    $errors[] = 'Invalid message number!'.$msgno.'<br>';
                                    continue;
                                }
                                imap_delete($inbox, $msgno);

                            }
                            imap_expunge($inbox);
                            imap_close($inbox);

                    return $errors;
                       
             

        }

        public function fetch_plain($imap_stream, $mid, $structure)
        {
           
           
                  
                     $body = imap_fetchbody($imap_stream, $mid, 1);
                     switch($structure->encoding)
                        {
                            case '3':
                            $body = imap_base64($body);
                            break;

                            case '4': 
                            $body = imap_qprint($body);
                            break;
                        }

                    return (!preg_match('/utf-?8/i', $structure->parameters[0]->value)) ? mb_convert_encoding($body, 'UTF-8', $structure->parameters[0]->value) : $body;
        
        }


       

}







       

