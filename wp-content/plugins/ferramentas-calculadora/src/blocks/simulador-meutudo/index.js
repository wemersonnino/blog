import { registerBlockType } from '@wordpress/blocks'
import { __ } from '@wordpress/i18n'
import { useBlockProps, RichText } from '@wordpress/block-editor'
import icons from '../../icons'

registerBlockType('ferramentas-calculadora/simulador-meutudo',{
    icon: icons.primary,
    edit(){
        const blockProps = useBlockProps();
        const {
            taxaConsignado,pagamentoConsignado,
            taxaPortabilidade,pagamentoPortabilidade,
            taxaSaqueAniversario,pagamentoSaqueAniversario
        } = attributes;
        return(
            <>
                <section {...blockProps}>
                    <h1>Test Block se error</h1>
                </section>
            </>
        )
    }
})