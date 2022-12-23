<?php
$this->setTitle('Todos');
?>
<a href="/todos/create"><button class="btn btn-primary">Create ToDo</button></a>
<h1>Todos</h1>

<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">Title</th>
            <th scope="col">Created</th>
            <th scope="col">Status</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (isset($todos)) :
            foreach ($todos as $todo) :
        ?>
                <tr>
                    <td><?php echo $todo->getTitle(); ?></td>
                    <td><?php echo $todo->getCreatedDateTime()->format(\DateTimeInterface::RFC2822); ?></td>
                    <td><?php echo $todo->getStatus()->value()  ?></td>
                    <td>
                        <form action="/todos/delete">
                            <input type="hidden" name="todoId" value="<?php echo $todo->getId(); ?>">
                            <input type="submit" value="Delete">
                        </form>
                        <?php if ($todo->getStatus()->value() == 'notStarted') : ?>
                            <form action="/todos/update">
                                <input type="hidden" name="todoId" value="<?php echo $todo->getId(); ?>">
                                <input type="submit" value="Complete">
                            </form>

                        <?php endif; ?>
                    </td>
                </tr>
        <?php

            endforeach;
        endif;
        ?>
    </tbody>
</table>