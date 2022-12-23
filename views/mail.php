<?php
$this->setTitle('Mailbox');
?>
<a href="/mails/send"><button class="btn btn-primary">Send Mail</button></a>
<h1>Inbox</h1>
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">Message</th>
            <th scope="col">From</th>
            <th scope="col">Status</th>
            <th scope="col">Received</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($mails)) :
            foreach ($mails as $mail) :
        ?>
                <tr>
                    <td><?php echo $mail->getSubject(); ?></td>
                    <td><?php echo $mail->getFrom()->getEmailAddress()->getName(); ?></td>
                    <td><?php echo $mail->getIsRead() ? "Read" : "Unread"; ?></td>
                    <td><?php echo $mail->getReceivedDateTime()->format(\DateTimeInterface::RFC2822); ?></td>
                </tr>
        <?php

            endforeach;
        endif;
        ?>
    </tbody>
    <?php



    ?>
</table>