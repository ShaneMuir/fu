import { render, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { store as coreDataStore } from '@wordpress/core-data';
import { decodeEntities } from '@wordpress/html-entities';
import { SearchControl, Spinner, Button, TextControl, Modal, SnackbarList } from '@wordpress/components';
import { store as noticesStore } from '@wordpress/notices';

function MyFirstApp() {
    const [searchTerm, setSearchTerm] = useState('');

    const { pages, hasResolved } = useSelect( select => {
        const query = {};
        if ( searchTerm ) {
            query.search = searchTerm;
        }
        const selectorArgs = ['postType', 'page', query];
        return {
            pages: select( coreDataStore ).getEntityRecords( ...selectorArgs ),
            hasResolved: select( coreDataStore ).hasFinishedResolution( 'getEntityRecords', selectorArgs ),
        };
    }, [searchTerm] );

    return (
        <div>
            <div className="list-controls">
                <SearchControl onChange={ setSearchTerm } value={ searchTerm }/>
                <CreatePageButton/>
            </div>
            <PagesList hasResolved={ hasResolved } pages={ pages }/>
            <Notifications />
        </div>
    )
}

function Notifications() {
    const notices = useSelect(
        ( select ) => select( noticesStore ).getNotices(),
        []
    );
    const { removeNotice } = useDispatch( noticesStore );
    const snackbarNotices = notices.filter( ({ type }) => type === 'snackbar' );

    return (
        <SnackbarList
            notices={ snackbarNotices }
            className="components-editor-notices__snackbar"
            onRemove={ removeNotice }
        />
    );
}

function PageEditButton ( { pageId } ) {
    const [ isOpen, setOpen ] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );

    return (
        <>
            <Button
                variant="primary"
                onClick={ openModal }
            >
                Edit
            </Button>
            { isOpen && (
                <Modal title="Edit Page" onRequestClose={ closeModal }>
                    <EditPageForm
                        pageId={ pageId }
                        onCancel={ closeModal }
                        onSaveFinished={ closeModal }
                    />
                </Modal>
            ) }
        </>
    );
}

function CreatePageButton() {
    const [isOpen, setOpen] = useState( false );
    const openModal = () => setOpen( true );
    const closeModal = () => setOpen( false );
    return (
        <>
            <Button onClick={ openModal } variant="primary">
                Create a new Page
            </Button>
            { isOpen && (
                <Modal onRequestClose={ closeModal } title="Create a new page">
                    <CreatePageForm
                        onCancel={ closeModal }
                        onSaveFinished={ closeModal }
                    />
                </Modal>
            ) }
        </>
    );
}

export function CreatePageForm( { onCancel, onSaveFinished } ) {
    const [title, setTitle] = useState('');
    const { lastError, isSaving } = useSelect(
        ( select ) => ( {
            lastError: select( coreDataStore )
                .getLastEntitySaveError( 'postType', 'page' ),
            isSaving: select( coreDataStore )
                .isSavingEntityRecord( 'postType', 'page' ),
        } ),
        []
    );

    const { saveEntityRecord } = useDispatch( coreDataStore );
    const handleSave = async () => {
        const savedRecord = await saveEntityRecord(
            'postType',
            'page',
            { title, status: 'publish' }
        );
        if ( savedRecord ) {
            onSaveFinished();
        }
    };

    return (
        <PageForm
            title={ title }
            onChangeTitle={ setTitle }
            hasEdits={ !!title }
            onSave={ handleSave }
            lastError={ lastError }
            onCancel={ onCancel }
            isSaving={ isSaving }
        />
    );
}

function DeletePageButton( { pageId } ) {
    const { createSuccessNotice, createErrorNotice } = useDispatch( noticesStore );
    const { getLastEntityDeleteError } = useSelect( coreDataStore )
    const handleDelete = async () => {
        const success = await deleteEntityRecord( 'postType', 'page', pageId);
        if ( success ) {
            createSuccessNotice( "The page was deleted!", {
                type: 'snackbar',
            } );
        } else {
            const lastError = getLastEntityDeleteError( 'postType', 'page', pageId );
            const message = ( lastError?.message || 'There was an error.' ) + ' Please refresh the page and try again.'
            createErrorNotice( message, {
                type: 'snackbar',
            } );
        }
    }

    const { deleteEntityRecord } = useDispatch( coreDataStore );
    const { isDeleting } = useSelect(
        select => ( {
            isDeleting: select( coreDataStore ).isDeletingEntityRecord( 'postType', 'page', pageId ),
        } ),
        [ pageId ]
    );

    return (
        <Button variant="primary" onClick={ handleDelete } disabled={ isDeleting }>
            { isDeleting ? (
                <>
                    <Spinner />
                    Deleting...
                </>
            ) : 'Delete' }
        </Button>
    );
}

export function EditPageForm( { pageId, onCancel, onSaveFinished } ) {
    const { page, lastError, isSaving, hasEdits } = useSelect(
        ( select ) => ( {
            page: select( coreDataStore ).getEditedEntityRecord( 'postType', 'page', pageId ),
            lastError: select( coreDataStore ).getLastEntitySaveError( 'postType', 'page', pageId ),
            isSaving: select( coreDataStore ).isSavingEntityRecord( 'postType', 'page', pageId ),
            hasEdits: select( coreDataStore ).hasEditsForEntityRecord( 'postType', 'page', pageId ),
        } ),
        [pageId]
    );

    const { saveEditedEntityRecord, editEntityRecord } = useDispatch( coreDataStore );
    const handleSave = async () => {
        const savedRecord = await saveEditedEntityRecord( 'postType', 'page', pageId );
        if ( savedRecord ) {
            onSaveFinished();
        }
    };
    const handleChange = ( title ) => editEntityRecord( 'postType', 'page', page.id, { title } );

    return (
        <PageForm
            title={ page.title }
            onChangeTitle={ handleChange }
            hasEdits={ hasEdits }
            lastError={ lastError }
            isSaving={ isSaving }
            onCancel={ onCancel }
            onSave={ handleSave }
        />
    );
}

export function PageForm( { title, onChangeTitle, hasEdits, lastError, isSaving, onCancel, onSave } ) {
    return (
        <div className="my-gutenberg-form">
            <TextControl
                label="Page title:"
                value={ title }
                onChange={ onChangeTitle }
            />
            { lastError ? (
                <div className="form-error">Error: { lastError.message }</div>
            ) : false }
            <div className="form-buttons">
                <Button
                    onClick={ onSave }
                    variant="primary"
                    disabled={ !hasEdits || isSaving }
                >
                    { isSaving ? (
                        <>
                            <Spinner/>
                            Saving
                        </>
                    ) : 'Save' }
                </Button>
                <Button
                    onClick={ onCancel }
                    variant="tertiary"
                    disabled={ isSaving }
                >
                    Cancel
                </Button>
            </div>
        </div>
    );
}

function PagesList( { hasResolved, pages } ) {
    if ( !hasResolved ) return <Spinner />;
    if ( !pages?.length ) return <p>No results.</p>;

    return (
      <table className='wp-list-table widefat fixed striped table-view-list'>
        <thead>
            <tr>
                <td>Title</td>
                <td style={{width: 190}}>Actions</td>
            </tr>
        </thead>
        <tbody>
            { pages?.map( page => (
              <tr key={ page.id }>
                  <td>{ decodeEntities( page.title.rendered ) }</td>
                  <td>
                      <div className="form-buttons">
                          <PageEditButton pageId={ page.id } />
                          <DeletePageButton pageId={ page.id }/>
                      </div>
                  </td>
              </tr>
            ) ) }
        </tbody>
      </table>
    );
}

window.addEventListener(
    'load',
    () => {
        const el = document.getElementById( 'my-first-gutenberg-app' );
        if ( el ) {
            render( <MyFirstApp />, el );
        }
    },
    false
);
